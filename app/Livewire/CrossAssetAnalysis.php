<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Asset;
use App\Models\Group;
use App\Models\ListData;
use App\Models\LogData;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CrossAssetAnalysis extends Component
{
    public $areas = [];
    public $search = '';

    public $expandedAreas = [];
    public $expandedGroups = [];
    public $expandedAssets = [];

    public $selectedAssetId = null;
    public $selectedParameters = [];
    public $parameters = [];

    public $chartData = [];
    public $chartTitle = 'Cross Asset Analysis';

    // Track the number of selected items
    public $selectedCount = 0;

    // Date range for chart data
    public $startDate;
    public $endDate;
    public $dateRange;
    public $interval = 'raw'; // Default to raw data

    // Tracking selected parameters by entity
    public $assetSelectionCount = [];
    public $groupSelectionCount = [];
    public $areaSelectionCount = [];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'dateRangeUpdated' => 'handleDateRangeUpdate'
    ];

    public function mount()
    {
        // Set default date range to last 30 days
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(3)->format('Y-m-d');
        $this->dateRange = $this->startDate . ' to ' . $this->endDate;
        $this->chartTitle = 'Cross Asset Analysis';



        // Initialize empty arrays
        $this->selectedParameters = [];
        $this->assetSelectionCount = [];
        $this->groupSelectionCount = [];
        $this->areaSelectionCount = [];

        $this->loadAreas();

        // Initialize empty chart data structure
        $this->chartData = [
            'title' => $this->chartTitle,
            'series' => [],
            'yAxis' => [],
            'dateRange' => [
                'start' => $this->startDate,
                'end' => $this->endDate
            ]
        ];

        // Dispatch initial empty chart data
        $this->dispatch('updateChart', $this->chartData);
    }

    public function loadAreas()
    {
        if (!empty($this->search)) {
            // First find matching assets
            $matchingAssetIds = Asset::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%'])
                ->pluck('id');

            // Then load only areas that have these assets
            $this->areas = Area::whereHas('groups.assets', function ($query) use ($matchingAssetIds) {
                $query->whereIn('assets.id', $matchingAssetIds);
            })
                ->with(['groups' => function ($groupQuery) use ($matchingAssetIds) {
                    // Only load groups that contain matching assets
                    $groupQuery->whereHas('assets', function ($assetQuery) use ($matchingAssetIds) {
                        $assetQuery->whereIn('assets.id', $matchingAssetIds);
                    });
                    // Load the matching assets for these groups
                    $groupQuery->with(['assets' => function ($assetQuery) use ($matchingAssetIds) {
                        $assetQuery->whereIn('assets.id', $matchingAssetIds);
                    }]);
                }])
                ->orderBy('name')
                ->get();
        } else {
            // When not searching, load all areas with their groups and assets
            $this->areas = Area::with(['groups.assets'])->orderBy('id')->get();
        }
    }

    public function updatedSearch()
    {
        // Reset expanded sections when searching
        if (!empty($this->search)) {
            // Find matching assets and expand their parent areas/groups
            $matchingAssets = Asset::where('name', 'like', '%' . $this->search . '%')->get();

            foreach ($matchingAssets as $asset) {
                if ($asset->group) {
                    $this->expandedGroups[] = $asset->group->id;
                    if ($asset->group->area) {
                        $this->expandedAreas[] = $asset->group->area_id;
                    }
                }
            }

            // Remove duplicates
            $this->expandedAreas = array_unique($this->expandedAreas);
            $this->expandedGroups = array_unique($this->expandedGroups);
        }

        // Reload areas with the search filter
        $this->loadAreas();
    }

    public function updatedStartDate()
    {
        if (!empty($this->selectedParameters)) {
            $this->generateChart();
        }
    }

    public function updatedEndDate()
    {
        if (!empty($this->selectedParameters)) {
            $this->generateChart();
        }
    }

    public function updatedDateRange($value)
    {
        if (strpos($value, ' to ') !== false) {
            list($start, $end) = explode(' to ', $value);
            $this->startDate = trim($start);
            $this->endDate = trim($end);
            if (!empty($this->selectedParameters)) {
                $this->generateChart();
                // Also notify JavaScript of the date change
                $this->dispatch('dateRangeChanged', [
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate
                ]);
            }
        }
    }

    public function handleDateRangeUpdate($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        if (!empty($this->selectedParameters)) {
            $this->generateChart();
        }
    }

    public function toggleArea($areaId)
    {
        if (in_array($areaId, $this->expandedAreas)) {
            $this->expandedAreas = array_diff($this->expandedAreas, [$areaId]);
        } else {
            $this->expandedAreas[] = $areaId;
        }
    }

    public function toggleGroup($groupId)
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupId]);
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    public function toggleAsset($assetId)
    {
        $this->selectedAssetId = $assetId;

        if (in_array($assetId, $this->expandedAssets)) {
            $this->expandedAssets = array_diff($this->expandedAssets, [$assetId]);
        } else {
            $this->expandedAssets[] = $assetId;
            $this->loadAssetParameters($assetId);
        }
    }

    public function loadAssetParameters($assetId)
    {
        $this->parameters = ListData::where('asset_id', $assetId)->orderBy('id')
            ->with(['asset.group.area', 'machineParameter', 'position', 'datvar'])
            ->get()
            ->map(function ($item) {
                // Create a display name based on the requirement
                $machineName = $item->machineParameter->name ?? '';
                $positionName = $item->position->name ?? '';
                $datvarName = $item->datvar->name ?? '';
                $datavarUnit = $item->datvar->unit ?? '';

                // Store area and group IDs
                $this->groupSelectionCount[$item->asset->group_id] = $this->groupSelectionCount[$item->asset->group_id] ?? 0;
                $this->areaSelectionCount[$item->asset->group->area_id] = $this->areaSelectionCount[$item->asset->group->area_id] ?? 0;

                // Check if uppercase names are the same, if so just show machineParameter
                if (
                    strtoupper($machineName) === strtoupper($positionName)
                ) {
                    $displayName = $datvarName . ' ' . $datavarUnit;
                } else {
                    $displayName = $machineName . ' ' . $positionName . ' ' . $datvarName . ' ' . $datavarUnit;
                }

                return [
                    'id' => $item->id,
                    'name' => $displayName,
                    'machine_parameter_id' => $item->machine_parameter_id,
                    'position_id' => $item->position_id,
                    'datvar_id' => $item->datvar_id,
                    'asset_id' => $item->asset_id,
                    'group_id' => $item->asset->group_id,
                    'area_id' => $item->asset->group->area_id
                ];
            });
    }

    public function toggleParameter($parameterId)
    {
        // Ensure parameterId is an integer
        $parameterId = (int)$parameterId;

        // Find parameter in the flattened array
        $index = array_search($parameterId, array_map('intval', $this->selectedParameters));
        $listData = ListData::with(['asset.group.area'])->find($parameterId);

        if (!$listData) return;

        $assetId = $listData->asset_id;
        $groupId = $listData->asset->group_id;
        $areaId = $listData->asset->group->area_id;

        if ($index !== false) {
            // Remove parameter if already selected
            unset($this->selectedParameters[$index]);
            // Re-index array and ensure all values are integers
            $this->selectedParameters = array_values(array_map('intval', $this->selectedParameters));

            // Update counts
            if (isset($this->assetSelectionCount[$assetId])) {
                $this->assetSelectionCount[$assetId]--;
                if ($this->assetSelectionCount[$assetId] <= 0) {
                    unset($this->assetSelectionCount[$assetId]);
                }
            }

            if (isset($this->groupSelectionCount[$groupId])) {
                $this->groupSelectionCount[$groupId]--;
                if ($this->groupSelectionCount[$groupId] <= 0) {
                    unset($this->groupSelectionCount[$groupId]);
                }
            }

            if (isset($this->areaSelectionCount[$areaId])) {
                $this->areaSelectionCount[$areaId]--;
                if ($this->areaSelectionCount[$areaId] <= 0) {
                    unset($this->areaSelectionCount[$areaId]);
                }
            }
        } else {
            // Add parameter to selection
            $this->selectedParameters[] = $parameterId;

            // Update counts
            if (!isset($this->assetSelectionCount[$assetId])) {
                $this->assetSelectionCount[$assetId] = 0;
            }
            $this->assetSelectionCount[$assetId]++;

            if (!isset($this->groupSelectionCount[$groupId])) {
                $this->groupSelectionCount[$groupId] = 0;
            }
            $this->groupSelectionCount[$groupId]++;

            if (!isset($this->areaSelectionCount[$areaId])) {
                $this->areaSelectionCount[$areaId] = 0;
            }
            $this->areaSelectionCount[$areaId]++;
        }

        $this->selectedCount = count($this->selectedParameters);

        // Ensure selectedParameters is always a flat array of integers
        $this->selectedParameters = array_values(array_map('intval', $this->selectedParameters));

        // Dispatch parameter selection event with the cleaned array
        $this->dispatch('parameterSelectionChanged', $this->selectedParameters);

        if ($this->selectedCount > 0) {
            // Make sure the dates are initialized
            if (empty($this->startDate) || empty($this->endDate)) {
                $this->endDate = Carbon::now()->format('Y-m-d');
                $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            }
            $this->generateChart();
        } else {
            $this->chartData = [
                'title' => $this->chartTitle,
                'series' => [],
                'yAxis' => [],
                'dateRange' => [
                    'start' => $this->startDate,
                    'end' => $this->endDate
                ]
            ];
            $this->dispatch('updateChart', $this->chartData);
        }
    }

    public function generateChart()
    {
        try {

            // Parse and format dates
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();

            $series = [];
            $yAxis = [];
            $annotations = []; // Add array for annotations

            // Flatten and sanitize the selectedParameters array
            $selectedIds = collect($this->selectedParameters)->flatten()->unique()->toArray();

            $listDataEntries = ListData::whereIn('id', $selectedIds)
                ->with(['asset', 'machineParameter', 'position', 'datvar'])
                ->get();


            foreach ($listDataEntries as $index => $listData) {
                // Create parameter display name
                $assetName = $listData->asset->name ?? '';
                $machineName = $listData->machineParameter->name ?? '';
                $positionName = $listData->position->name ?? '';
                $datvarName = $listData->datvar->name ?? '';
                $datavarUnit = $listData->datvar->unit ? '(' . $listData->datvar->unit . ')' : '';

                if (
                    strtoupper($machineName) === strtoupper($positionName)
                ) {
                    $displayName = $assetName . ' - ' . $datvarName . ' ' . $datavarUnit;
                } else {
                    $displayName = implode(' ', array_filter([$assetName, $machineName, $positionName, $datvarName, $datavarUnit]));
                }

                // Get base query for log data
                $logQuery = LogData::where('list_data_id', $listData->id)
                    ->whereBetween('created_at', [
                        $startDate->format('Y-m-d H:i:s'),
                        $endDate->format('Y-m-d H:i:s')
                    ])
                    ->orderBy('created_at', 'asc');

                // Apply interval aggregation if specified
                $dataPoints = [];

                // Process data based on interval
                if ($this->interval === 'raw') {
                    // Get raw data without aggregation
                    $logData = $logQuery->get();
                    foreach ($logData as $item) {
                        if ($item->value !== null && $item->created_at) {
                            $timestamp = $item->created_at->timestamp * 1000; // Convert to milliseconds
                            $value = (float) $item->value;
                            $dataPoints[] = [$timestamp, $value];
                        }
                    }
                } else {
                    // Fetch all data for this parameter
                    $logData = $logQuery->get();

                    // Group and aggregate based on interval
                    $groupedData = null;

                    switch ($this->interval) {
                        case '3-minutes':
                            $groupedData = $this->groupLogDataByMinutes($logData, 3);
                            break;
                        case '10-minutes':
                            $groupedData = $this->groupLogDataByMinutes($logData, 10);
                            break;
                        case '15-minutes':
                            $groupedData = $this->groupLogDataByMinutes($logData, 15);
                            break;
                        case '30-minutes':
                            $groupedData = $this->groupLogDataByMinutes($logData, 30);
                            break;
                        case 'hour':
                            $groupedData = $this->groupLogDataByHours($logData, 1);
                            break;
                        case '4-hours':
                            $groupedData = $this->groupLogDataByHours($logData, 4);
                            break;
                        case '6-hours':
                            $groupedData = $this->groupLogDataByHours($logData, 6);
                            break;
                        case '12-hours':
                            $groupedData = $this->groupLogDataByHours($logData, 12);
                            break;
                        case 'day':
                            $groupedData = $this->groupLogDataByDay($logData);
                            break;
                    }

                    if ($groupedData && count($groupedData) > 0) {
                        foreach ($groupedData as $timestamp => $value) {
                            $dataPoints[] = [(int)$timestamp, $value];
                        }
                    }
                }


                if (!empty($dataPoints)) {

                    // Add series data
                    $series[] = [
                        'name' => $displayName,
                        'data' => $dataPoints
                    ];

                    // Calculate min/max from actual data
                    $values = array_column($dataPoints, 1);
                    $dataMin = min($values);
                    $dataMax = max($values);

                    // Add padding to min/max for better visualization
                    $padding = ($dataMax - $dataMin) * 0.1;
                    $yAxisMin = $dataMin - $padding;
                    $yAxisMax = $dataMax + $padding;

                    // Add Y-axis configuration with proper positioning
                    $yAxisConfig = [
                        'name' => $displayName,
                        'min' => $listData->min ?? $yAxisMin,
                        'max' => $listData->max ?? $yAxisMax,
                        'unit' => $listData->unit ?? '',
                        'seriesName' => $displayName,
                        'opposite' => $index % 2 !== 0, // Alternate sides for Y-axes
                        'decimalsInFloat' => 2,
                        'labels' => [
                            'formatter' => "function(val) { return val.toFixed(2); }"
                        ]
                    ];

                    // If only one parameter is selected, add warning and danger limits
                    if (count($listDataEntries) === 1) {
                        $warningLimit = $listData->warning_limit;
                        $dangerLimit = $listData->danger_limit;

                        $annotations = [];

                        if ($warningLimit !== null) {
                            $annotations[] = [
                                'y' => $warningLimit,
                                'borderColor' => '#ffc107',
                                'label' => [
                                    'text' => 'Warning ',
                                    'style' => [
                                        'color' => '#fff',
                                        'background' => '#ffc107'
                                    ]
                                ]
                            ];
                        }

                        if ($dangerLimit !== null) {
                            $annotations[] = [
                                'y' => $dangerLimit,
                                'borderColor' => '#dc3545',
                                'label' => [
                                    'text' => 'Danger ',
                                    'style' => [
                                        'color' => '#fff',
                                        'background' => '#dc3545'
                                    ]
                                ]
                            ];
                        }

                        $yAxisConfig['annotations'] = $annotations;
                    }

                    $yAxis[] = $yAxisConfig;
                }
            }

            // Create title with interval information
            $title = $this->chartTitle;
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

            $this->chartData = [
                'title' => $title,
                'series' => $series,
                'yAxis' => $yAxis,
                'interval' => $this->interval,
                'dateRange' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ]
            ];


            $this->dispatch('updateChart', $this->chartData);

            return $this->chartData;
        } catch (\Exception $e) {


            $this->chartData = [
                'title' => $this->chartTitle,
                'series' => [],
                'yAxis' => [],
                'error' => $e->getMessage(),
                'dateRange' => [
                    'start' => $this->startDate,
                    'end' => $this->endDate
                ]
            ];

            return $this->chartData;
        }
    }

    /**
     * Group log data by minute intervals
     *
     * @param Collection $logData
     * @param int $minutes
     * @return array
     */
    protected function groupLogDataByMinutes($logData, $minutes)
    {
        $result = [];

        $grouped = $logData->groupBy(function ($item) use ($minutes) {
            $timestamp = Carbon::parse($item->created_at);
            $minute = $timestamp->minute;
            $roundedMinute = floor($minute / $minutes) * $minutes;
            $timestamp->minute($roundedMinute)->second(0);
            return $timestamp->format('Y-m-d H:i');
        });

        foreach ($grouped as $timeKey => $group) {
            // Calculate average value for this time interval
            $avgValue = $group->first()->value;

            // Convert time to timestamp in milliseconds for chart
            $timestamp = Carbon::parse($timeKey)->timestamp * 1000;

            $result[$timestamp] = $avgValue;
        }

        return $result;
    }

    /**
     * Group log data by hour intervals
     *
     * @param Collection $logData
     * @param int $hours
     * @return array
     */
    protected function groupLogDataByHours($logData, $hours)
    {
        $result = [];

        $grouped = $logData->groupBy(function ($item) use ($hours) {
            $timestamp = Carbon::parse($item->created_at);
            $hour = $timestamp->hour;
            $roundedHour = floor($hour / $hours) * $hours;
            $timestamp->hour($roundedHour)->minute(0)->second(0);
            return $timestamp->format('Y-m-d H:i');
        });

        foreach ($grouped as $timeKey => $group) {
            // Calculate average value for this time interval
            $avgValue = $group->first()->value;

            // Convert time to timestamp in milliseconds for chart
            $timestamp = Carbon::parse($timeKey)->timestamp * 1000;

            $result[$timestamp] = $avgValue;
        }

        return $result;
    }

    /**
     * Group log data by days
     *
     * @param Collection $logData
     * @return array
     */
    protected function groupLogDataByDay($logData)
    {
        $result = [];

        $grouped = $logData->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d');
        });

        foreach ($grouped as $timeKey => $group) {
            // Calculate average value for this day
            $avgValue = $group->first()->value;

            // Convert time to timestamp in milliseconds for chart
            $timestamp = Carbon::parse($timeKey)->timestamp * 1000;

            $result[$timestamp] = $avgValue;
        }

        return $result;
    }

    public function render()
    {
        return view('livewire.cross-asset-analysis');
    }
}
