<?php

namespace App\Exports;

use App\Models\MepaTransacao;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class RelatorioPagamentoExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithEvents
{

    use Exportable;

    protected $search;

    public function __construct(Array $params)
    {
        if(Gate::denies('view_administrador')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

        $this->search = $params;
    }


    public function headings(): array
    {
        return [
            'Payment Code',
            'Data',
            'Nome Pessoa',
            'Curso',
            'Total',
            'Status'
        ];
    }

    public function map($faturamento): array
    {
        return [
            $faturamento->payment_code,
            Date::stringToExcel($faturamento->data_criacao_formatada),
            $faturamento->user->name,
            $faturamento->curso->nome,
            $faturamento->amount ?? 0,
            $faturamento->status_dashboard
        ];
    }

    public function collection()
    {

        $search_RP = $this->search;

        $user = Auth()->User();

        $faturamentos_RP = [];
        if($search_RP['data_inicio_RP'] || $search_RP['data_fim_RP'] || $search_RP['categoria_RP'] || $search_RP['curso_RP']){
            $faturamentos_RP = MepaTransacao::join('mepa_situacaos', 'mepa_transacaos.mepa_situacao_id', '=', 'mepa_situacaos.id')
                                        ->where('mepa_transacaos.tipo_compra', 'curso')
                                        ->where(function($query) use ($search_RP){
                                            if($search_RP['situacao_RP']){
                                                if($search_RP['situacao_RP'] == 'PG'){
                                                    $query->whereIn('mepa_situacaos.status', ['approved','finish_free', 'donate_free']);
                                                }elseif($search_RP['situacao_RP'] == 'PD'){
                                                    $query->whereIn('mepa_situacaos.status', ['in_process', 'pending']);
                                                }elseif($search_RP['situacao_RP'] == 'RJ'){
                                                    $query->whereIn('mepa_situacaos.status', ['rejected', 'not_found']);
                                                }
                                            }
                                        })
                                        ->leftJoin('cursos', 'mepa_transacaos.curso_id', '=', 'cursos.id')
                                        ->where(function($query) use ($search_RP){
                                            if($search_RP['curso_RP']){
                                                $query->where('cursos.id', $search_RP['curso_RP']);
                                            }
                                        })
                                        ->leftJoin('categorias', 'cursos.categoria_id', '=', 'categorias.id')
                                        ->where(function($query) use ($search_RP){
                                            if($search_RP['categoria_RP']){
                                                $query->where('categorias.id', $search_RP['categoria_RP']);
                                            }
                                        })
                                        ->where(function($query) use ($search_RP){
                                            if($search_RP['data_inicio_RP'] && $search_RP['data_fim_RP']){
                                                $query->where('mepa_transacaos.created_at', '>=', $search_RP['data_inicio_RP']);
                                                $query->where('mepa_transacaos.created_at', '<=', $search_RP['data_fim_RP']);
                                            } elseif($search_RP['data_inicio_RP']){
                                                $query->where('mepa_transacaos.created_at', '>=', $search_RP['data_inicio_RP']);
                                            } elseif($search_RP['data_fim_RP']){
                                                $query->where('mepa_transacaos.created_at', '<=', $search_RP['data_fim_RP']);
                                            }
                                        })
                                    ->select('mepa_transacaos.*')
                                    ->orderBy('mepa_transacaos.created_at', 'desc')
                                    ->get();
        }

        return $faturamentos_RP;
    }


    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1  => ['font' => ['bold' => true],
                     'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                    ],
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'B' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]],
            'D' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]],
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'F' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {

                $event->sheet->setAutoFilter('A1:F1');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);

                // Cabeçalho
                $event->sheet->getDelegate()->getStyle('A1:F1')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('D9D9D9');
            },
        ];
    }

}
