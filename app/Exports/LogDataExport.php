<?php

namespace App\Exports;

use App\Models\LogData;
use Maatwebsite\Excel\Concerns\FromCollection;
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
use Illuminate\Support\Collection;
use App\Models\ListData;

class LogDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles, WithEvents
{
    protected $assetId;
    protected $parameters;
    protected $startDate;
    protected $endDate;
    protected $interval;
    protected $rowNumber = 0;
    protected $parameterNames = [];

    /**
     * Constructor
     *
     * @param int $assetId
     * @param array $parameters
     * @param string $startDate
     * @param string $endDate
     * @param string $interval
     */
    public function __construct($assetId, $parameters, $startDate, $endDate, $interval = 'raw')
    {
        $this->assetId = $assetId;
        $this->parameters = $parameters;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->interval = $interval;
        $this->loadParameterNames();
    }

    /**
     * Load parameter names for later use in headings and mapping
     */
    protected function loadParameterNames()
    {
        $listData = ListData::whereIn('id', $this->parameters)
            ->with(['machineParameter', 'position', 'datvar'])
            ->get();

        foreach ($listData as $parameter) {
            $machineParamName = $parameter->machineParameter->name ?? 'N/A';
            $positionName = $parameter->position->name ?? 'N/A';
            $datvarName = $parameter->datvar->name ?? 'N/A';
            $unit = $parameter->datvar->unit ?? '';

            $parameterName = $datvarName;
            if (!(strtoupper($machineParamName) === strtoupper($positionName))) {
                $parameterName = implode(' - ', array_filter([
                    $machineParamName !== 'N/A' ? $machineParamName : null,
                    $positionName !== 'N/A' ? $positionName : null,
                    $datvarName !== 'N/A' ? $datvarName : null
                ]));
            }

            // Store parameter name and unit for use in headings
            $this->parameterNames[$parameter->id] = [
                'name' => $parameterName,
                'unit' => $unit
            ];
        }
    }

    /**
     * Get data collection for the export
     */
    public function collection()
    {
        // We need to group the log data by timestamp to create rows with multiple parameters
        $logs = LogData::where('asset_id', $this->assetId)
            ->whereIn('list_data_id', $this->parameters)
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->orderBy('created_at')
            ->get();

        // Group logs based on interval setting
        switch ($this->interval) {
            case '3-minutes':
                $groupedLogs = $this->groupByMinutes($logs, 3);
                break;
            case '10-minutes':
                $groupedLogs = $this->groupByMinutes($logs, 10);
                break;
            case '15-minutes':
                $groupedLogs = $this->groupByMinutes($logs, 15);
                break;
            case '30-minutes':
                $groupedLogs = $this->groupByMinutes($logs, 30);
                break;
            case 'hour':
                $groupedLogs = $this->groupByHours($logs, 1);
                break;
            case '4-hours':
                $groupedLogs = $this->groupByHours($logs, 4);
                break;
            case '6-hours':
                $groupedLogs = $this->groupByHours($logs, 6);
                break;
            case '12-hours':
                $groupedLogs = $this->groupByHours($logs, 12);
                break;
            case 'day':
                $groupedLogs = $logs->groupBy(function ($log) {
                    return Carbon::parse($log->created_at)->format('Y-m-d');
                });
                break;
            case 'raw':
            default:
                // Default to raw data - group by minute
                $groupedLogs = $logs->groupBy(function ($log) {
                    return Carbon::parse($log->created_at)->format('Y-m-d H:i');
                });
                break;
        }

        // Create a collection of grouped logs
        return $groupedLogs;
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        $timeFormat = 'YYYY-MM-DD H:I';

        // Modify time format based on interval
        if ($this->interval === 'day') {
            $timeFormat = 'YYYY-MM-DD';
        }

        // Create value suffix based on interval (avg, min, max)
        $valueSuffix = '';
        if ($this->interval !== 'raw') {
            $valueSuffix = ' (Avg)'; // Using average for all aggregated data
        }

        $headings = [
            'No',
            'Log Time (' . $timeFormat . ')'
        ];

        // Add parameter columns
        foreach ($this->parameterNames as $paramData) {
            $heading = $paramData['name'];
            if (!empty($paramData['unit'])) {
                $heading .= ' (' . $paramData['unit'] . ')';
            }
            // Add value suffix for aggregated data
            $heading .= $valueSuffix;
            $headings[] = $heading;
        }

        return $headings;
    }

    /**
     * Map the data for export
     */
    public function map($logs): array
    {
        $this->rowNumber++;

        // Get timestamp from the first log in the group
        $timestamp = Carbon::parse($logs->first()->created_at)->format('Y-m-d H:i');

        // Start with row number and timestamp
        $row = [$this->rowNumber, $timestamp];

        // Add a column for each parameter (in the same order as headings)
        foreach ($this->parameters as $paramId) {
            // Find log for this parameter at this timestamp
            $logValue = null;
            foreach ($logs as $log) {
                if ($log->list_data_id == $paramId) {
                    $logValue = $log->value;
                    break;
                }
            }

            // Add value (or empty cell if no data)
            $row[] = $logValue !== null ? number_format($logValue, 2) : '';
        }

        return $row;
    }

    /**
     * Set the title of the sheet
     */
    public function title(): string
    {
        $title = 'Log Data Export';

        // Add interval information if not raw
        if ($this->interval !== 'raw') {
            $intervalText = '';
            switch ($this->interval) {
                case '3-minutes':
                    $intervalText = '3 Minutes';
                    break;
                case '10-minutes':
                    $intervalText = '10 Minutes';
                    break;
                case '15-minutes':
                    $intervalText = '15 Minutes';
                    break;
                case '30-minutes':
                    $intervalText = '30 Minutes';
                    break;
                case 'hour':
                    $intervalText = '1 Hour';
                    break;
                case '4-hours':
                    $intervalText = '4 Hours';
                    break;
                case '6-hours':
                    $intervalText = '6 Hours';
                    break;
                case '12-hours':
                    $intervalText = '12 Hours';
                    break;
                case 'day':
                    $intervalText = '1 Day';
                    break;
                default:
                    $intervalText = $this->interval;
            }

            $title .= ' - ' . $intervalText . ' Interval';
        }

        return $title;
    }

    /**
     * Style the sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0070C0'], // Blue header
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * Group logs by specified minute interval
     *
     * @param Collection $logs
     * @param int $minuteInterval
     * @return Collection
     */
    protected function groupByMinutes($logs, $minuteInterval)
    {
        return $logs->groupBy(function ($log) use ($minuteInterval) {
            $timestamp = Carbon::parse($log->created_at);
            $minute = $timestamp->minute;
            $roundedMinute = floor($minute / $minuteInterval) * $minuteInterval;
            $timestamp->minute($roundedMinute)->second(0);
            return $timestamp->format('Y-m-d H:i');
        });
    }

    /**
     * Group logs by specified hour interval
     *
     * @param Collection $logs
     * @param int $hourInterval
     * @return Collection
     */
    protected function groupByHours($logs, $hourInterval)
    {
        return $logs->groupBy(function ($log) use ($hourInterval) {
            $timestamp = Carbon::parse($log->created_at);
            $hour = $timestamp->hour;
            $roundedHour = floor($hour / $hourInterval) * $hourInterval;
            $timestamp->hour($roundedHour)->minute(0)->second(0);
            return $timestamp->format('Y-m-d H:i');
        });
    }

    /**
     * Register events
     */
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
                $sheet->getStyle($cellRange)->applyFromArray([
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
