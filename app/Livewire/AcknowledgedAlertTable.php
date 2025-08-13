<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\DataAlarm;
use App\Models\ListData;
use App\Models\Area;
use App\Models\Group;
use App\Exports\AcknowledgedAlarmsExport;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AcknowledgedAlertTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $area = '';
    public $group = '';
    public $asset = '';
    public $parameter = '';
    public $alertType = '';
    public $alarmCause = '';
    public $acknowledgePerson = '';
    public $dateRange = '';
    public $startDate;
    public $endDate;

    protected $queryString = [
        'area' => ['except' => ''],
        'group' => ['except' => ''],
        'asset' => ['except' => ''],
        'parameter' => ['except' => ''],
        'alertType' => ['except' => ''],
        'alarmCause' => ['except' => ''],
        'acknowledgePerson' => ['except' => ''],
        'dateRange' => ['except' => '']
    ];

    public function mount()
    {
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->dateRange = $this->startDate . ' to ' . $this->endDate;
    }

    public function updatedDateRange($value)
    {
        if ($value) {
            $dates = explode(' to ', $value);
            $this->startDate = $dates[0];
            $this->endDate = $dates[1] ?? $dates[0];
        }
        $this->dispatch('filterChanged');
    }

    public function updatedArea()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedGroup()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedAsset()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedParameter()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedAlertType()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedAlarmCause()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedAcknowledgePerson()
    {
        $this->dispatch('filterChanged');
    }

    protected function formatParameterName($listData)
    {
        $machineParamName = $listData->machineParameter->name ?? '';
        $positionName = $listData->position->name ?? '';
        $datvarName = $listData->datvar->name ?? '';

        // Remove empty values and N/A
        $parts = array_filter([
            $machineParamName,
            $positionName,
            $datvarName
        ], function ($value) {
            return $value && $value !== 'N/A';
        });

        // If all parts are the same (case insensitive), return just one
        if (count($parts) > 1) {
            $allSame = true;
            $firstPart = strtoupper(reset($parts));
            foreach ($parts as $part) {
                if (strtoupper($part) !== $firstPart) {
                    $allSame = false;
                    break;
                }
            }
            if ($allSame) {
                return reset($parts);
            }
        }

        // Otherwise combine all parts
        return implode(' - ', $parts) ?: 'N/A';
    }

    public function render()
    {
        // Query alerts with filters
        $query = DataAlarm::query()
            ->with(['listData.asset.group.area', 'listData.machineParameter', 'listData.position', 'listData.datvar', 'acknowledgedByUser'])
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
            ->when($this->alarmCause, function ($query) {
                $query->where('alarm_cause', $this->alarmCause);
            })
            ->when($this->acknowledgePerson, function ($query) {
                $query->whereHas('acknowledgedByUser', function ($q) {
                    $q->where('name', 'like', '%' . $this->acknowledgePerson . '%');
                });
            })
            ->when($this->dateRange, function ($query) {
                if ($this->startDate && $this->endDate) {
                    $query->whereBetween('start_time', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ]);
                }
            })
            ->where('acknowledged', true)
            ->orderBy('acknowledged_at', 'desc');

        $alerts = $query->paginate(10);

        // Area: from master Area
        $areas = Area::orderBy('name')->pluck('name');

        // Group: filtered by area
        $groups = $this->area
            ? Group::whereHas('area', function ($q) {
                $q->where('name', $this->area);
            })->orderBy('name')->pluck('name')
            : Group::orderBy('name')->pluck('name');

        // Asset: filtered by group/area (kedua filter aktif bersamaan)
        $assetQuery = Asset::query();
        if ($this->area) {
            $assetQuery->whereHas('group.area', function ($q) {
                $q->where('name', $this->area);
            });
        }
        if ($this->group) {
            $assetQuery->whereHas('group', function ($q) {
                $q->where('name', $this->group);
            });
        }
        $assets = $assetQuery->orderBy('name')->pluck('name');

        // Parameter: filtered by asset/group/area (semua filter aktif bersamaan)
        $paramQuery = ListData::with(['machineParameter', 'position', 'datvar']);
        if ($this->area) {
            $paramQuery->whereHas('asset.group.area', function ($q) {
                $q->where('name', $this->area);
            });
        }
        if ($this->group) {
            $paramQuery->whereHas('asset.group', function ($q) {
                $q->where('name', $this->group);
            });
        }
        if ($this->asset) {
            $paramQuery->whereHas('asset', function ($q) {
                $q->where('name', $this->asset);
            });
        }
        $parameters = $paramQuery->get()->map(function ($item) {
            $name = collect([
                $item->machineParameter->name ?? null,
                $item->position->name ?? null,
                $item->datvar->name ?? null,
            ])->filter()->unique()->implode(' - ');
            return [
                'id' => $item->id,
                'name' => $name,
            ];
        })->unique('name')->values();

        // Alert types: filtered by current filter
        $alertTypes = DataAlarm::query()
            ->where('acknowledged', true)
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
            ->distinct()
            ->pluck('alert_type');

        // Get alarm causes
        $alarmCauses = DataAlarm::query()
            ->where('acknowledged', true)
            ->whereNotNull('alarm_cause')
            ->distinct()
            ->pluck('alarm_cause');

        // Get acknowledgers (users who acknowledged alarms)
        $acknowledgers = DataAlarm::query()
            ->where('acknowledged', true)
            ->whereNotNull('acknowledged_by')
            ->whereHas('acknowledgedByUser')
            ->with('acknowledgedByUser')
            ->get()
            ->map(function ($alarm) {
                return $alarm->acknowledgedByUser->name;
            })
            ->unique()
            ->values();

        return view('livewire.acknowledged-alert-table', [
            'alerts' => $alerts,
            'areas' => $areas,
            'groups' => $groups,
            'assets' => $assets,
            'parameters' => $parameters,
            'alertTypes' => $alertTypes,
            'alarmCauses' => $alarmCauses,
            'acknowledgers' => $acknowledgers,
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['area', 'group', 'asset', 'parameter', 'alertType', 'alarmCause', 'acknowledgePerson']);
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->dateRange = $this->startDate . ' to ' . $this->endDate;
    }

    public function exportToExcel()
    {
        $fileName = 'acknowledged_alerts_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new AcknowledgedAlarmsExport(
                $this->area,
                $this->group,
                $this->asset,
                $this->parameter,
                $this->alertType,
                $this->alarmCause,
                $this->acknowledgePerson,
                $this->startDate,
                $this->endDate
            ),
            $fileName
        );
    }
}
