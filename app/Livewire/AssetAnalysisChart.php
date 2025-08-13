<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\ListData;
use App\Models\LogData;
use App\Models\MachineParameter;
use App\Models\Datactual;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class AssetAnalysisChart extends Component
{
    public $assetId = null;
    public $selectedParameters = [];
    public $startDate;
    public $endDate;
    public $chartData = [];
    public $readyToLoad = false; // New property to track if chart should be loaded
    public $availableParameters = []; // New property to store available parameters
    public $crossAnalysis = false; // New property for cross analysis mode
    public $interval = 'raw'; // Default interval - raw data without aggregation

    protected $listeners = [
        'assetSelected' => 'setAsset',
        'parametersSelected' => 'updateSelectedParameters',
        'dateRangeChanged' => 'updateDateRange',
        'performCrossAnalysis' => 'performCrossAnalysis'
    ];

    protected function getListeners()
    {
        // Daftar lebih banyak event yang mungkin untuk memastikan tidak ada yang terlewat
        return [
            'assetSelected' => 'setAsset',
            'parametersSelected' => 'captureParametersEvent',
            'asset-analysis-parameters:parametersSelected' => 'captureParametersEvent',
            'asset-analysis-parameters::parametersSelected' => 'captureParametersEvent',
            'AssetAnalysisParameters:parametersSelected' => 'captureParametersEvent',
            'custom-parameters-selected' => 'captureCustomEvent',  // Menangkap browser event
            'dateRangeChanged' => 'updateDateRange',
            'performCrossAnalysis' => 'performCrossAnalysis',
            '*' => 'handleAnyEvent',  // Menangkap semua event sebagai fallback
        ];
    }

    public function boot() {}

    public function mount($assetId = null, $parameters = [])
    {


        $this->assetId = $assetId;
        $this->crossAnalysis = false;
        $this->readyToLoad = false;

        // Set default date range to the last 7 days
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(3)->format('Y-m-d');

        // Initialize selectedParameters from passed parameters or from asset default parameters
        if (!empty($parameters)) {
            $this->selectedParameters = is_array($parameters) ? $parameters : [$parameters];
        } elseif ($assetId) {
            // Load default parameters for this asset if no specific parameters provided
            $this->loadDefaultParameters();
        }

        // Initialize empty chart data
        $this->chartData = [
            'series' => [],
            'categories' => [],
            'title' => 'Asset Parameter Analysis',
            'yAxis' => [],
            'assetInfo' => [
                'id' => $this->assetId,
                'name' => $assetId ? $this->getAssetName($assetId) : 'No Asset Selected'
            ],
            'interval' => $this->interval
        ];
    }

    public function hydrate() {}

    public function loadDefaultParameters()
    {
        if (!$this->assetId) {
            return;
        }

        try {
            // Get the most frequently used parameters for this asset
            $frequentParams = MachineParameter::whereHas('datactuals', function ($query) {
                $query->where('asset_id', $this->assetId);
            })
                ->withCount(['datactuals' => function ($query) {
                    $query->where('asset_id', $this->assetId);
                }])
                ->orderBy('datactuals_count', 'desc')
                ->take(3)
                ->pluck('id')
                ->toArray();

            if (!empty($frequentParams)) {
                $this->selectedParameters = $frequentParams;
            }
        } catch (\Exception $e) {
        }
    }

    public function updated($name, $value)
    {
        if (in_array($name, ['assetId', 'selectedParameters', 'startDate', 'endDate'])) {
            $this->loadChartData($this->crossAnalysis);
        }

        if ($name === 'crossAnalysis') {
            $this->loadChartData($value);
        }
    }

    public function setAsset($assetId)
    {
        $this->assetId = $assetId;
        $this->selectedParameters = []; // Reset selected parameters when changing asset
        $this->readyToLoad = false; // Set to false until parameters are selected
        $this->dispatch('assetChanged', $this->assetId); // Notify other components that asset has changed
    }

    public function updateSelectedParameters($parameters)
    {

        // Process incoming parameters, ensuring they're properly formatted
        if (empty($parameters)) {
            $this->selectedParameters = [];
        } else {
            // Handle both array and non-array formats
            if (is_array($parameters)) {
                // Filter out empty values and sanitize
                $this->selectedParameters = array_filter(array_map(function ($param) {
                    return is_array($param) ? (int)$param[0] : (int)$param;
                }, $parameters));
            } else {
                $this->selectedParameters = [(int)$parameters];
            }
        }



        // Only load chart data if we have both parameters and an asset
        if (!empty($this->selectedParameters) && $this->assetId) {
            $this->readyToLoad = true;
        } else {

            $this->readyToLoad = false;
        }
    }

    /**
     * Handle date range changes from the frontend
     * This method is flexible to accept either individual parameters or a single array parameter
     */
    public function updateDateRange(...$args)
    {
        // Support both (startDate, endDate) and ([startDate, endDate]) and (['startDate'=>..., 'endDate'=>...])
        $startDate = null;
        $endDate = null;

        if (count($args) === 1 && is_array($args[0])) {
            // Livewire v3 style: dispatch('dateRangeChanged', {startDate, endDate})
            $arr = $args[0];
            if (isset($arr['startDate']) && isset($arr['endDate'])) {
                $startDate = $arr['startDate'];
                $endDate = $arr['endDate'];
            } elseif (array_values($arr) === $arr && count($arr) === 2) {
                // Numeric array [startDate, endDate]
                $startDate = $arr[0];
                $endDate = $arr[1];
            }
        } elseif (count($args) === 2) {
            $startDate = $args[0];
            $endDate = $args[1];
        }

        // Fallback to default if not set
        $this->startDate = $startDate ?: date('Y-m-d', strtotime('-7 days'));
        $this->endDate = $endDate ?: date('Y-m-d');



        // Add this line to ensure date range is valid and readyToLoad is set to true
        if ($this->assetId && !empty($this->selectedParameters)) {
            $this->readyToLoad = true;
        }

        $this->loadChartData();
    }

    public function performCrossAnalysis()
    {
        if (count($this->selectedParameters) < 2) {
            // Use session flash instead of dispatch for notifications
            session()->flash('warning', 'Please select at least two parameters for cross analysis');
            return;
        }

        $this->loadChartData(true);
    }

    private function cleanArray($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                // Skip Livewire's special array marker
                if (is_array($item) && isset($item['s']) && $item['s'] === 'arr') continue;
                $result[] = $this->cleanArray($item);
            }
            return $result;
        }
        return $data;
    }

    public function loadChartData($crossAnalysis = false)
    {
        if (!$this->readyToLoad || empty($this->selectedParameters) || !$this->assetId) {
            return;
        }

        try {
            // Initialize the chart structure
            $series = [];
            $yAxisList = [];
            $parameterLabels = [];

            // Get the ListData entries that match the selected IDs
            $listDataEntries = ListData::whereIn('id', $this->selectedParameters)
                ->with(['machineParameter', 'asset', 'position', 'datvar'])
                ->get();

            if ($listDataEntries->isEmpty()) {
                return;
            }

            // Parse date range
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();

            // Process each parameter's data
            foreach ($listDataEntries as $entry) {
                $paramId = $entry->id;

                // Format parameter display name
                $positionName = strtoupper($entry->position->name ?? '');
                $machineParamName = strtoupper($entry->machineParameter->name ?? '');
                $datvarName = strtoupper($entry->datvar->name ?? '');

                if (
                    $positionName === $machineParamName
                ) {
                    $paramName = $entry->asset->name . ' - ' . $entry->datvar->name . ' ' . $entry->datvar->unit;
                } else {
                    $paramName = $entry->asset->name . ' ' . ($entry->position->name ?? '') . ' ' .
                        ($entry->machineParameter->name ?: $entry->name ?? "Parameter $paramId") . ' ' .
                        ($entry->datvar->name ?? '') . ' (' . $entry->datvar->unit . ')';
                }

                // Fetch log data within date range with a base query
                $logQuery = LogData::where('list_data_id', $paramId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'asc');

                // Get the warning and danger values from the ListData table
                $warningValue = $entry->warning_limit;
                $dangerValue = $entry->danger_limit;

                // Apply interval aggregation if not using raw data
                if ($this->interval === 'raw') {
                    // Use raw data without aggregation
                    $logData = $logQuery->get();

                    // Format data points
                    $seriesData = $logData->map(function ($item) {
                        return [
                            $item->created_at->format('Y-m-d H:i'),
                            (float) $item->value
                        ];
                    })->toArray();
                } else {
                    // Apply aggregation based on the selected interval
                    switch ($this->interval) {
                        case '3-minutes':
                            // Group by 3-minute intervals
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                return Carbon::parse($date->created_at)->format('Y-m-d H:i');
                                // This groups by minute, we'll further group by 3 minutes below
                            })->map(function ($group) {
                                $timestamp = Carbon::parse($group->first()->created_at);
                                // Round to nearest 3-minute interval
                                $minute = $timestamp->minute;
                                $roundedMinute = floor($minute / 3) * 3;
                                $timestamp->minute($roundedMinute)->second(0);

                                return [
                                    'timestamp' => $timestamp->format('Y-m-d H:i'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case '10-minutes':
                            // Group by 10-minute intervals
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                return Carbon::parse($date->created_at)->format('Y-m-d H:i');
                                // This groups by minute, we'll further group by 10 minutes below
                            })->map(function ($group) {
                                $timestamp = Carbon::parse($group->first()->created_at);
                                // Round to nearest 10-minute interval
                                $minute = $timestamp->minute;
                                $roundedMinute = floor($minute / 10) * 10;
                                $timestamp->minute($roundedMinute)->second(0);

                                return [
                                    'timestamp' => $timestamp->format('Y-m-d H:i'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case '15-minutes':
                            // Group by 15-minute intervals
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                return Carbon::parse($date->created_at)->format('Y-m-d H:i');
                                // This groups by minute, we'll further group by 15 minutes below
                            })->map(function ($group) {
                                $timestamp = Carbon::parse($group->first()->created_at);
                                // Round to nearest 15-minute interval
                                $minute = $timestamp->minute;
                                $roundedMinute = floor($minute / 15) * 15;
                                $timestamp->minute($roundedMinute)->second(0);

                                return [
                                    'timestamp' => $timestamp->format('Y-m-d H:i'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case '30-minutes':
                            // Group by 30-minute intervals
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                return Carbon::parse($date->created_at)->format('Y-m-d H:i');
                                // This groups by minute, we'll further group by 30 minutes below
                            })->map(function ($group) {
                                $timestamp = Carbon::parse($group->first()->created_at);
                                // Round to nearest 30-minute interval
                                $minute = $timestamp->minute;
                                $roundedMinute = floor($minute / 30) * 30;
                                $timestamp->minute($roundedMinute)->second(0);

                                return [
                                    'timestamp' => $timestamp->format('Y-m-d H:i:s'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case 'hour':
                            // Group by hour
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                return Carbon::parse($date->created_at)->format('Y-m-d H');
                            })->map(function ($group) {
                                return [
                                    'timestamp' => Carbon::parse($group->first()->created_at)->startOfHour()->format('Y-m-d H:i'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case '4-hours':
                            // Group by 4-hour intervals
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                $hour = Carbon::parse($date->created_at)->hour;
                                // Round to nearest 4-hour interval
                                $roundedHour = floor($hour / 4) * 4;
                                return Carbon::parse($date->created_at)->format('Y-m-d') . ' ' . $roundedHour;
                            })->map(function ($group) {
                                $timestamp = Carbon::parse($group->first()->created_at);
                                $hour = $timestamp->hour;
                                $roundedHour = floor($hour / 4) * 4;
                                $timestamp->hour($roundedHour)->minute(0)->second(0);

                                return [
                                    'timestamp' => $timestamp->format('Y-m-d H:i]'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case '6-hours':
                            // Group by 6-hour intervals
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                $hour = Carbon::parse($date->created_at)->hour;
                                // Round to nearest 6-hour interval
                                $roundedHour = floor($hour / 6) * 6;
                                return Carbon::parse($date->created_at)->format('Y-m-d') . ' ' . $roundedHour;
                            })->map(function ($group) {
                                $timestamp = Carbon::parse($group->first()->created_at);
                                $hour = $timestamp->hour;
                                $roundedHour = floor($hour / 6) * 6;
                                $timestamp->hour($roundedHour)->minute(0)->second(0);

                                return [
                                    'timestamp' => $timestamp->format('Y-m-d H:i'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case '12-hours':
                            // Group by 12-hour intervals
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                $hour = Carbon::parse($date->created_at)->hour;
                                // Round to nearest 12-hour interval
                                $roundedHour = floor($hour / 12) * 12;
                                return Carbon::parse($date->created_at)->format('Y-m-d') . ' ' . $roundedHour;
                            })->map(function ($group) {
                                $timestamp = Carbon::parse($group->first()->created_at);
                                $hour = $timestamp->hour;
                                $roundedHour = floor($hour / 12) * 12;
                                $timestamp->hour($roundedHour)->minute(0)->second(0);

                                return [
                                    'timestamp' => $timestamp->format('Y-m-d H:i'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;

                        case 'day':
                            // Group by day
                            $logData = $logQuery->get()->groupBy(function ($date) {
                                return Carbon::parse($date->created_at)->format('Y-m-d');
                            })->map(function ($group) {
                                return [
                                    'timestamp' => Carbon::parse($group->first()->created_at)->startOfDay()->format('Y-m-d H:i'),
                                    'value' => $group->first()->value
                                ];
                            })->values();
                            break;



                        default:
                            // Fallback to raw data
                            $logData = $logQuery->get();
                            break;
                    }

                    // Format aggregated data points
                    if ($this->interval !== 'raw') {
                        $seriesData = $logData->map(function ($item) {
                            return [
                                $item['timestamp'],
                                (float) $item['value']
                            ];
                        })->toArray();
                    } else {
                        $seriesData = $logData->map(function ($item) {
                            return [
                                $item->created_at->format('Y-m-d H:i:s'),
                                (float) $item->value
                            ];
                        })->toArray();
                    }
                }

                // Add to series
                $series[] = [
                    'name' => $paramName,
                    'data' => $seriesData,
                ];

                // Add Y-axis configuration
                $yAxisList[] = [
                    'name' => $paramName,
                    'min' => $entry->min ?? null,
                    'max' => $entry->max ?? null,
                    'unit' => $entry->unit ?? '',
                    'warning' => $warningValue,
                    'danger' => $dangerValue
                ];
            }

            // Generate interval text for title
            $intervalText = '';
            if ($this->interval !== 'raw') {
                switch ($this->interval) {
                    case '3-minutes':
                        $intervalText = ' (3 Minutes Interval)';
                        break;
                    case '10-minutes':
                        $intervalText = ' (10 Minutes Interval)';
                        break;
                    case '15-minutes':
                        $intervalText = ' (15 Minutes Interval)';
                        break;
                    case '30-minutes':
                        $intervalText = ' (30 Minutes Interval)';
                        break;
                    case 'hour':
                        $intervalText = ' (1 Hour Interval)';
                        break;
                    case '4-hours':
                        $intervalText = ' (4 Hours Interval)';
                        break;
                    case '6-hours':
                        $intervalText = ' (6 Hours Interval)';
                        break;
                    case '12-hours':
                        $intervalText = ' (12 Hours Interval)';
                        break;
                    case 'day':
                        $intervalText = ' (1 Day Interval)';
                        break;
                    default:
                        $intervalText = " ({$this->interval} Interval)";
                }
            }

            // Update chart data with proper date range
            $this->chartData = [
                'series' => $series,
                'yAxis' => $yAxisList,
                'title' => 'Asset Parameter Analysis' . $intervalText,
                'crossAnalysis' => $crossAnalysis,
                'interval' => $this->interval,
                'dateRange' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ]
            ];

            $this->dispatch('updateChart', $this->chartData);
        } catch (\Exception $e) {
            // Log the error...
        }
    }

    public function captureCustomEvent($event)
    {


        if (isset($event['parameters'])) {
            $this->updateSelectedParameters($event['parameters']);
        }
    }

    public function handleAnyEvent($event, $data)
    {


        // Check if this is possibly a parameters event
        if ($event === 'parametersSelected' || strpos($event, 'parameter') !== false) {
            if (is_array($data)) {
                $this->updateSelectedParameters($data);
            }
        }
    }

    public function captureParametersEvent($parameters = null)
    {


        // Forward to the actual handler if parameters provided
        if ($parameters !== null) {
            $this->updateSelectedParameters($parameters);
        } else {
        }
    }

    /**
     * Safely get the asset name by ID
     *
     * @param int $assetId
     * @return string
     */
    protected function getAssetName($assetId)
    {
        $asset = Asset::find($assetId);

        if (!$asset) {
            return "Asset $assetId";
        }

        // Check if we're dealing with a model object or a collection
        if (is_object($asset) && method_exists($asset, 'getAttribute')) {
            return $asset->getAttribute('name') ?? "Asset $assetId";
        } elseif ($asset instanceof \Illuminate\Support\Collection) {
            return $asset->get('name') ?? $asset->first()['name'] ?? "Asset $assetId";
        }

        return "Asset $assetId"; // fallback
    }

    public function render()
    {
        return view('livewire.asset-analysis-chart');
    }

    private function getAvailableParameters()
    {
        return MachineParameter::where('asset_id', $this->assetId)->get();
    }
}
