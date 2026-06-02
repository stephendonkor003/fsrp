<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeIndicatorsManagementReportExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        protected array $rows,
        protected string $searchTerm = ''
    ) {}

    public function array(): array
    {
        $data = [];

        $data[] = [
            'Program',
            'Project',
            'Activity',
            'Sub-Activity',
            'Indicator',
            'Owner Type',
            'FSRP Component',
            'FSRP Subcomponent',
            'Level',
            'Frequency',
            'Baseline Type',
            'Baseline Period',
            'Baseline Value',
            'Disaggregation',
            'LOP Target',
            'Period Target',
            'Period Achievement',
            'Period Performance %',
            'LOP Performance %',
            'Responsible Party/Person',
            'Methodology',
            'Primary Source Type',
            'Primary Source Value',
            'Definition',
            'Target',
            'Actual',
            'Achievement',
            'Status',
            'Performance Remarks',
            'Notes',
        ];

        foreach ($this->rows as $row) {
            $data[] = [
                $row['program'] ?? 'N/A',
                $row['project'] ?? 'N/A',
                $row['activity'] ?? 'N/A',
                $row['sub_activity'] ?? 'N/A',
                $row['indicator_name'] ?? 'N/A',
                $row['owner_type'] ?? 'N/A',
                $row['fsrp_component'] ?? 'N/A',
                $row['fsrp_subcomponent'] ?? 'N/A',
                $row['indicator_level'] ?? 'N/A',
                $row['frequency'] ?? 'N/A',
                $row['baseline_type'] ?? 'N/A',
                $row['baseline_period'] ?? 'N/A',
                $row['baseline_value'] ?? 'N/A',
                $row['disaggregation'] ?? 'N/A',
                $row['lop_target'] ?? 'N/A',
                $row['reporting_period_target'] ?? 'N/A',
                $row['reporting_period_achievement'] ?? 'N/A',
                $row['reporting_period_performance'] ?? 'N/A',
                $row['lop_performance'] ?? 'N/A',
                $row['responsible'] ?? 'N/A',
                $row['methodology'] ?? 'N/A',
                $row['primary_source_type'] ?? 'N/A',
                $row['primary_source_value'] ?? 'N/A',
                $row['definition'] ?? 'N/A',
                $row['target'] ?? 'N/A',
                $row['actual'] ?? 'N/A',
                $row['achievement'] ?? 'N/A',
                $row['status'] ?? 'N/A',
                $row['performance_remarks'] ?? 'N/A',
                $row['notes'] ?? 'N/A',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E2E8F0']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 28,
            'C' => 26,
            'D' => 26,
            'E' => 32,
            'F' => 16,
            'G' => 26,
            'H' => 28,
            'I' => 16,
            'J' => 18,
            'K' => 16,
            'L' => 18,
            'M' => 18,
            'N' => 20,
            'O' => 16,
            'P' => 16,
            'Q' => 16,
            'R' => 18,
            'S' => 18,
            'T' => 30,
            'U' => 24,
            'V' => 20,
            'W' => 30,
            'X' => 30,
            'Y' => 14,
            'Z' => 14,
            'AA' => 14,
            'AB' => 14,
            'AC' => 30,
            'AD' => 30,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$highestColumn}1");

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()
                    ->setVertical('top')
                    ->setWrapText(true);

                $sheet->getStyle("A1:{$highestColumn}1")
                    ->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');
            },
        ];
    }
}
