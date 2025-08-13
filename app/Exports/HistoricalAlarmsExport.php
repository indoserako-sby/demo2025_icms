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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class HistoricalAlarmsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles, WithEvents, WithCustomStartCell
{
    protected $assetId;
    protected $parameter;
    protected $alertType;
    protected $alarmCause;
    protected $acknowledgePerson;
    protected $startDate;
    protected $endDate;
    protected $assetName;
    private $rowNumber = 0;

    public function __construct($assetId, $parameter, $alertType, $alarmCause, $acknowledgePerson, $startDate, $endDate, $assetName)
    {
        $this->assetId = $assetId;
        $this->parameter = $parameter;
        $this->alertType = $alertType;
        $this->alarmCause = $alarmCause;
        $this->acknowledgePerson = $acknowledgePerson;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->assetName = $assetName;
    }

    public function query()
    {
        $query = DataAlarm::query()
            ->with(['listData.machineParameter', 'listData.position', 'listData.datvar', 'acknowledgedByUser'])
            ->whereHas('listData', function ($query) {
                $query->where('asset_id', $this->assetId);
            })
            ->where('acknowledged', true)  // Hanya menampilkan alarm yang belum di-acknowledge
            ->when($this->parameter, function ($query) {
                return $query->where('list_data_id', $this->parameter);
            })
            ->when($this->alertType, function ($query) {
                return $query->where('alert_type', $this->alertType);
            })
            ->when($this->alarmCause, function ($query) {
                return $query->where('alarm_cause', $this->alarmCause);
            })
            ->when($this->acknowledgePerson, function ($query) {
                return $query->whereHas('acknowledgedByUser', function ($q) {
                    $q->where('name', 'like', '%' . $this->acknowledgePerson . '%');
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                return $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            })
            ->orderBy('created_at', 'desc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Parameter',
            'Alert Type',
            'Reason',
            'Range Time',
            'Date Acknowledge',
            'Alarm Cause',
            'Note',
            'Acknowledge Person',
            'Time Maintenance',
            'Maintenance Person'
        ];
    }

    public function map($alarm): array
    {
        // Increment row number
        $this->rowNumber++;

        $machineParamName = $alarm->listData->machineParameter->name ?? '';
        $positionName = $alarm->listData->position->name ?? '';
        $datvarName = $alarm->listData->datvar->name ?? '';

        $parameterName = $machineParamName;
        if (!(strtoupper($machineParamName) === strtoupper($positionName))) {
            $parameterName = implode(' - ', array_filter([
                $machineParamName ?: 'N/A',
                $positionName ?: 'N/A',
                $datvarName ?: 'N/A'
            ]));
        }

        // Get alarm reason
        $reason = $this->getAlarmReason($alarm);

        // Format range time
        $rangeTime = ($alarm->start_time ? $alarm->start_time->format('Y-m-d H:i') : 'now') .
            ' - ' .
            ($alarm->end_time ? $alarm->end_time->format('Y-m-d H:i') : 'now');

        // Format time maintenance range
        $timeMaintenance = ($alarm->starttimemaintenance ? $alarm->starttimemaintenance->format('Y-m-d H:i') : 'N/A') .
            ' - ' .
            ($alarm->endtimemaintenance ? $alarm->endtimemaintenance->format('Y-m-d H:i') : 'N/A');

        return [
            $this->rowNumber,
            $parameterName,
            ucfirst($alarm->alert_type),
            $reason,
            $rangeTime,
            $alarm->acknowledged_at ? $alarm->acknowledged_at->format('Y-m-d H:i') : 'Not Acknowledged',
            ucfirst($alarm->alarm_cause ?? 'N/A'),
            $alarm->notes ?? 'N/A',
            $alarm->acknowledgedByUser->name ?? 'N/A',
            $timeMaintenance,
            $alarm->machine_person ?? 'N/A'
        ];
    }

    public function title(): string
    {
        return 'Historical Alarms - ' . $this->assetName;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'] // Blue color
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'], // White text for better contrast
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
            },
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    private function getAlarmReason($alarm)
    {
        $value = number_format($alarm->value, 2);
        $listData = $alarm->listData;

        if ($alarm->alert_type === 'danger') {
            return "The parameter value ({$value} {$listData->datvar->unit}) exceeds the specified danger limit ({$alarm->danger} {$listData->datvar->unit}). ";
        } elseif ($alarm->alert_type === 'warning') {
            return "The parameter value ({$value} {$listData->datvar->unit}) exceeds the specified warning limit ({$alarm->warning} {$listData->datvar->unit}). ";
        }

        return "Unknown reason";
    }
}
