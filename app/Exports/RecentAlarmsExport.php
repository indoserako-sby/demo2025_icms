<?php

namespace App\Exports;

use App\Models\DataAlarm;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class RecentAlarmsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles, WithEvents
{
    protected $area;
    protected $group;
    protected $asset;
    protected $parameter;
    protected $alertType;
    protected $startDate;
    protected $endDate;
    protected $rowNumber = 0;

    public function __construct($area, $group, $asset, $parameter, $alertType, $startDate, $endDate)
    {
        $this->area = $area;
        $this->group = $group;
        $this->asset = $asset;
        $this->parameter = $parameter;
        $this->alertType = $alertType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return DataAlarm::query()
            ->with(['listData.asset.group.area', 'listData.machineParameter', 'listData.position', 'listData.datvar'])
            ->when($this->area, function ($query) {
                $query->whereHas('listData.asset.group.area', function ($q) {
                    $q->where('name', $this->area);
                });
            })
            ->when($this->group, function ($query) {
                $query->whereHas('listData.asset.group', function ($q) {
                    $q->where('name', $this->group);
                });
            })
            ->when($this->asset, function ($query) {
                $query->whereHas('listData.asset', function ($q) {
                    $q->where('name', $this->asset);
                });
            })
            ->when($this->parameter, function ($query) {
                $query->whereHas('listData', function ($q) {
                    $q->where('id', $this->parameter);
                });
            })
            ->when($this->alertType, function ($query) {
                $query->where('alert_type', $this->alertType);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('start_time', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            })
            ->where('acknowledged', false)
            ->orderBy('start_time', 'desc');
    }

    public function headings(): array
    {
        return [
            'No.',
            'Area',
            'Group',
            'Asset',
            'Parameter',
            'Alert Type',
            'Start Time',
            'End Time'
        ];
    }

    public function map($alert): array
    {
        $this->rowNumber++;

        // Format parameter name
        $machineParamName = $alert->listData->machineParameter->name ?? 'N/A';
        $positionName = $alert->listData->position->name ?? 'N/A';
        $datvarName = $alert->listData->datvar->name ?? 'N/A';

        $parameterName = $machineParamName;
        if (!(strtoupper($machineParamName) === strtoupper($positionName) &&
            strtoupper($machineParamName) === strtoupper($datvarName))) {
            $parameterName = implode(' - ', array_filter([
                $machineParamName !== 'N/A' ? $machineParamName : null,
                $positionName !== 'N/A' ? $positionName : null,
                $datvarName !== 'N/A' ? $datvarName : null
            ]));
        }

        return [
            $this->rowNumber,
            $alert->listData->asset->group->area->name ?? 'N/A',
            $alert->listData->asset->group->name ?? 'N/A',
            $alert->listData->asset->name ?? 'N/A',
            $parameterName,
            ucfirst($alert->alert_type),
            $alert->start_time ? $alert->start_time->format('Y-m-d H:i:s') : 'N/A',
            $alert->end_time ? $alert->end_time->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Recent Alerts';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '007bff'] // Primary color
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'], // White text
                    'bold' => true
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Get highest row and column
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Apply borders to all cells
                $cellRange = 'A1:' . $highestColumn . $highestRow;
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Auto size columns for better readability
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
