<?php

namespace App\Exports;

use App\Models\DataAlarm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CurrentAlarmsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $assetId;
    protected $search;
    protected $alertTypeFilter;
    protected $startDate;
    protected $endDate;

    public function __construct($assetId, $search, $alertTypeFilter, $startDate, $endDate)
    {
        $this->assetId = $assetId;
        $this->search = $search;
        $this->alertTypeFilter = $alertTypeFilter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = DataAlarm::query()
            ->whereIn('list_data_id', function ($query) {
                $query->select('id')
                    ->from('list_data')
                    ->where('asset_id', $this->assetId);
            })
            ->where('acknowledged', false)
            ->whereDate('start_time', '>=', $this->startDate)
            ->whereDate('start_time', '<=', $this->endDate)
            ->with(['listData.machineParameter', 'listData.position', 'listData.datvar']);

        if ($this->search) {
            $query->whereHas('listData.machineParameter', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->alertTypeFilter) {
            $query->where('alert_type', $this->alertTypeFilter);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Parameter',
            'Alert Type',
            'Reason',
            'Value',
            'Range Time',
        ];
    }

    public function map($alarm): array
    {
        static $row = 0;
        $row++;
        $machineParamName = $alarm->listData->machineParameter->name ?? '';
        $positionName = $alarm->listData->position->name ?? '';
        $datvarName = $alarm->listData->datvar->name ?? '';
        $parameter = implode(' - ', array_filter([
            $machineParamName ?: 'N/A',
            $positionName ?: 'N/A',
            $datvarName ?: 'N/A'
        ]));
        // Reason logic (similar to getAlarmReason)
        $value = number_format($alarm->value, 2);
        $listData = $alarm->listData;
        if ($alarm->alert_type === 'danger') {
            $reason = "The parameter value ({$value} " . ($listData->datvar->unit ?? '') . ") exceeds the specified danger limit ({$alarm->danger} " . ($listData->datvar->unit ?? '') . "). ";
        } elseif ($alarm->alert_type === 'warning') {
            $reason = "The parameter value ({$value} " . ($listData->datvar->unit ?? '') . ") exceeds the specified warning limit ({$alarm->warning} " . ($listData->datvar->unit ?? '') . "). ";
        } else {
            $reason = "Unknown reason";
        }
        $alarm->value = $value . ' ' . ($listData->datvar->unit ?? '');
        $alarm->start_time = $alarm->start_time->format('Y-m-d H:i');
        $rangeTime = ($alarm->start_time ? $alarm->start_time->format('Y-m-d H:i') : 'now') .
            ' - ' .
            ($alarm->end_time ? $alarm->end_time->format('Y-m-d H:i') : 'now');
        return [
            $row,
            $parameter,
            $alarm->alert_type,
            $reason,
            $alarm->value,
            $rangeTime,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header: blue background, white text, bold
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0070C0'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        // All cells: border
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        // Set cell background for alert_type only (not whole row)
        for ($row = 2; $row <= $highestRow; $row++) {
            $alertType = $sheet->getCell('C' . $row)->getValue();
            if ($alertType === 'danger') {
                $sheet->getStyle('C' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8D7DA'], // Bootstrap danger
                    ],
                ]);
            } elseif ($alertType === 'warning') {
                $sheet->getStyle('C' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3CD'], // Bootstrap warning
                    ],
                ]);
            }
        }
        // Auto width for all columns
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        return [];
    }
}
