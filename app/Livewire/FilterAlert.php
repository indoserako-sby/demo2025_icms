<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Area;
use App\Models\Group;
use App\Models\Asset;
use App\Models\ListData;
use App\Models\DataAlarm;
use Illuminate\Support\Carbon;

class FilterAlert extends Component
{
    public $area = '';
    public $group = '';
    public $asset = '';
    public $parameter = '';
    public $alertType = '';
    public $dateRange = '';
    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->resetFilters();
    }

    public function getAreasProperty()
    {
        return Area::orderBy('name')->pluck('name');
    }

    public function getGroupsProperty()
    {
        if ($this->area) {
            return Group::whereHas('area', function ($q) {
                $q->where('name', $this->area);
            })->orderBy('name')->pluck('name');
        }
        return Group::orderBy('name')->pluck('name');
    }

    public function getAssetsProperty()
    {
        $query = Asset::query();
        if ($this->area) {
            $query->whereHas('group.area', function ($q) {
                $q->where('name', $this->area);
            });
        }
        if ($this->group) {
            $query->whereHas('group', function ($q) {
                $q->where('name', $this->group);
            });
        }
        return $query->orderBy('name')->pluck('name');
    }

    public function getParametersProperty()
    {
        $query = ListData::with(['machineParameter', 'position', 'datvar']);
        if ($this->asset) {
            $query->whereHas('asset', function ($q) {
                $q->where('name', $this->asset);
            });
        } elseif ($this->group) {
            $query->whereHas('asset.group', function ($q) {
                $q->where('name', $this->group);
            });
        } elseif ($this->area) {
            $query->whereHas('asset.group.area', function ($q) {
                $q->where('name', $this->area);
            });
        }
        return $query->get()->map(function ($item) {
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
    }

    public function getAlertTypesProperty()
    {
        return DataAlarm::distinct()->pluck('alert_type')->filter()->values();
    }

    public function resetFilters()
    {
        $this->area = '';
        $this->group = '';
        $this->asset = '';
        $this->parameter = '';
        $this->alertType = '';
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->dateRange = $this->startDate . ' to ' . $this->endDate;
    }

    public function render()
    {
        return view('livewire.filter-alert', [
            'areas' => $this->areas,
            'groups' => $this->groups,
            'assets' => $this->assets,
            'parameters' => $this->parameters,
            'alertTypes' => $this->alertTypes,
        ]);
    }
}
