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

class RelatorioVendaExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithEvents
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
            'Data Início',
            'Data Fim',
            'Curso',
            'Categoria',
            'Qtd. Vendas',
            'Valor Total'
        ];
    }

    public function map($faturamento): array
    {
        return [
            Date::stringToExcel($this->search['data_inicio_RV']),
            Date::stringToExcel($this->search['data_fim_RV']),
            $faturamento->curso_nome,
            $faturamento->categoria_nome,
            $faturamento->qtd,
            $faturamento->faturamento
        ];
    }

    public function collection()
    {

        $search_RV = $this->search;

        $user = Auth()->User();

        $faturamentos_RV = MepaTransacao::join('mepa_situacaos', 'mepa_transacaos.mepa_situacao_id', '=', 'mepa_situacaos.id')
                                    ->join('cursos', 'mepa_transacaos.curso_id', '=', 'cursos.id')
                                    ->join('categorias', 'cursos.categoria_id', '=', 'categorias.id')
                                    ->where('mepa_situacaos.status', 'approved')
                                    ->where(function($query) use ($search_RV){
                                        if($search_RV['data_inicio_RV'] && $search_RV['data_fim_RV']){
                                            $query->where('mepa_transacaos.created_at', '>=', $search_RV['data_inicio_RV']);
                                            $query->where('mepa_transacaos.created_at', '<=', $search_RV['data_fim_RV']);
                                        } elseif($search_RV['data_inicio_RV']){
                                            $query->where('mepa_transacaos.created_at', '>=', $search_RV['data_inicio_RV']);
                                        } elseif($search_RV['data_fim_RV']){
                                            $query->where('mepa_transacaos.created_at', '<=', $search_RV['data_fim_RV']);
                                        }
                                    })                                    
                                    ->groupBy(DB::raw('cursos.nome, categorias.nome'))
                                    ->select(DB::raw('cursos.nome as curso_nome, categorias.nome as categoria_nome,
                                                        COUNT(mepa_transacaos.curso_id) AS qtd,
                                                        SUM(mepa_transacaos.amount) AS faturamento'))
                                    ->orderBy('cursos.nome')
                                    ->get();        

        return $faturamentos_RV;
    }


    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {

                $event->sheet->setAutoFilter('A1:F1');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(20);

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
