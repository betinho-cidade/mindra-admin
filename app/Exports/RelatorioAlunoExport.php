<?php

namespace App\Exports;

use App\Models\CursoRealizado;
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

class RelatorioAlunoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{

    use Exportable;


    public function __construct()
    {
        if(Gate::denies('view_administrador')){
            abort('403', 'Página não disponível');
            //return redirect()->back();
        }

    }


    public function headings(): array
    {
        return [
            'Aluno',
            'E-mail',
            'Curso',
            'Progresso',
            'Dias'
        ];
    }

    public function map($aluno): array
    {
        return [
            $aluno->user_name,
            $aluno->user_email,
            $aluno->curso_nome,
            $aluno->curso->percentual_realizado($aluno->user_id) . '%',
            $aluno->dias
        ];
    }

    public function collection()
    {

        $user = Auth()->User();

        $alunos_AA = CursoRealizado::join('cursos', 'curso_realizados.curso_id', '=', 'cursos.id')
                                    ->join('users', 'curso_realizados.user_id', '=', 'users.id')
                                    ->whereNotIn('curso_realizados.situacao', ['P'])
                                    ->select(DB::raw('users.id AS user_id, users.email AS user_email, users.name as user_name, cursos.id as curso_id, cursos.nome as curso_nome, curso_realizados.situacao,
                                                      CASE WHEN (curso_realizados.situacao = \'F\') THEN DATEDIFF(curso_realizados.data_fim, curso_realizados.data_inicio) ELSE DATEDIFF(CURRENT_DATE, curso_realizados.data_inicio) END AS dias'))                                         
                                    ->orderBy('users.name')
                                    ->orderBy('cursos.nome')
                                    ->get();    
        return $alunos_AA;
    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1  => ['font' => ['bold' => true],
                     'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                    ],
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]],
            'B' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]],
            'C' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]],
            'D' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {

                $event->sheet->setAutoFilter('A1:D1');

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(10);

                // Cabeçalho
                $event->sheet->getDelegate()->getStyle('A1:E1')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('D9D9D9');
            },
        ];
    }

}
