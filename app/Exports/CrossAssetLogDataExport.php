<?php

namespace App\Exports;

use App\Models\LogData;
use App\Models\ListData;
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

class CrossAssetLogDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles, WithEvents
{
    protected $selectedParameters;
    protected $startDate;
    protected $endDate;
    protected $interval;
    protected $rowNumber = 0;
    protected $parameterNames = [];
    protected $collectedData;

    /**
     * Constructor
     *
     * @param array $selectedParameters
     * @param string $startDate
     * @param string $endDate
     * @param string $interval
     */
    public function __construct($selectedParameters, $startDate, $endDate, $interval = 'raw')
    {


        // Handle the specific case where we receive ["8,10"] format
        if (
            is_array($selectedParameters) && count($selectedParameters) === 1 &&
            is_string($selectedParameters[0]) && strpos($selectedParameters[0], ',') !== false
        ) {
            $selectedParameters = explode(',', $selectedParameters[0]);
        }

        // Convert all parameters to integers
        $this->selectedParameters = array_map(function ($param) {
            return (int) trim($param);
        }, (array) $selectedParameters);

        // Ensure we have valid parameters
        if (empty($this->selectedParameters)) {
            throw new \InvalidArgumentException("No valid parameters selected");
        }



        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->interval = $interval;
        $this->loadParameterNames();
        $this->collectData();
    }

    protected function loadParameterNames()
    {
        $listData = ListData::whereIn('id', $this->selectedParameters)
            ->with(['asset', 'machineParameter', 'position', 'datvar'])
            ->get();

        foreach ($listData as $parameter) {
            // Create parameter display name
            $assetName = $parameter->asset->name ?? 'N/A';
            $machineName = $parameter->machineParameter->name ?? 'N/A';
            $positionName = $parameter->position->name ?? 'N/A';
            $datvarName = $parameter->datvar->name ?? 'N/A';
            $datavarUnit = $parameter->datvar->unit ?? '';

            if (
                strtoupper($machineName) === strtoupper($positionName)
            ) {
                $displayName = "{$assetName} - {$datvarName}";
            } else {
                $displayName = implode(' - ', array_filter([
                    $assetName,
                    $machineName !== 'N/A' ? $machineName : null,
                    $positionName !== 'N/A' ? $positionName : null,
                    $datvarName !== 'N/A' ? $datvarName : null
                ]));
            }

            $this->parameterNames[$parameter->id] = [
                'name' => $displayName,
                'unit' => $datavarUnit
            ];
        }
    }

    /**
     * Collect and group log data based on interval setting
     */
    protected function collectData()
    {
        // Get all log data within the date range, ordered by time
        $logs = LogData::whereIn('list_data_id', array_values($this->selectedParameters))
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

        // Process the grouped logs to create a map of parameter values
        $this->collectedData = $groupedLogs->map(function ($groupedLogs) {
            // For each timestamp, create a map of parameter_id => value
            $parameterValues = [];
            foreach ($this->selectedParameters as $paramId) {
                // Get the first matching log for any interval type
                $logsForParam = $groupedLogs->where('list_data_id', $paramId);
                if ($logsForParam->count() > 0) {
                    $parameterValues[$paramId] = $logsForParam->first()->value;
                } else {
                    $parameterValues[$paramId] = null;
                }
            }
            return $parameterValues;
        });
    }

    public function collection()
    {
        return $this->collectedData->map(function ($values, $timestamp) {
            return [
                'timestamp' => $timestamp,
                'values' => $values
            ];
        });
    }

    public function headings(): array
    {
        $timeFormat = 'YYYY-MM-DD H:i';

        // Modify time format based on interval
        if ($this->interval === 'day') {
            $timeFormat = 'YYYY-MM-DD';
        }

        // Create value suffix based on interval (avg, min, max)
        $valueSuffix = '';
        if ($this->interval !== 'raw') {
            $valueSuffix = ' (First)'; // Using first data for all aggregated data
        }

        $headings = [
            'No',
            'Log Time (' . $timeFormat . ')'
        ];

        // Add parameter columns with their full names
        foreach ($this->selectedParameters as $paramId) {
            $paramData = $this->parameterNames[$paramId];
            $heading = $paramData['name'];
            if (!empty($paramData['unit'])) {
                $heading .= ' (' . $paramData['unit'] . ')';
            }
            $headings[] = $heading;
        }

        return $headings;
    }

    public function map($row): array
    {
        $this->rowNumber++;

        // Start with row number and timestamp
        $mappedRow = [
            $this->rowNumber,
            $row['timestamp']
        ];

        // Add values for each parameter, formatted to 2 decimal places if numeric
        foreach ($this->selectedParameters as $paramId) {
            $value = $row['values'][$paramId] ?? '';
            if (is_numeric($value)) {
                $value = number_format((float)$value, 2, '.', '');
            }
            $mappedRow[] = $value;
        }

        return $mappedRow;
    }

    public function title(): string
    {
        $title = 'Cross Asset Log Data';

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
}
