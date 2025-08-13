<?php

namespace App\Livewire;

use App\Models\DataAlarm;
use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Exports\CurrentAlarmsExport;
use Maatwebsite\Excel\Facades\Excel;

class CurrentAlarmTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';


    public $assetId = null;
    public $search = '';
    public $alertTypeFilter = '';
    public $asset = null;
    public $startDate;
    public $endDate;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'alertTypeFilter', 'startDate', 'endDate'];

    protected $listeners = [
        'assetSelected' => 'loadAsset',
        'alarmAcknowledged' => '$refresh'
    ];

    public function mount()
    {
        // Set default date range to last month
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function loadAsset($assetId)
    {
        $this->assetId = $assetId;
        $this->asset = Asset::find($assetId);
        $this->resetPage();
    }

    public function updatedStartDate($value)
    {
        $this->resetPage();
    }

    public function updatedEndDate($value)
    {
        $this->resetPage();
    }

    public function openAcknowledgeModal($alarmId)
    {
        $this->dispatch('openAcknowledgeModal', alarmId: $alarmId);
    }

    public function exportExcel()
    {
        $fileName = 'current_alarms_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(
            new CurrentAlarmsExport(
                $this->assetId,
                $this->search,
                $this->alertTypeFilter,
                $this->startDate,
                $this->endDate
            ),
            $fileName
        );
    }

    public function getFormattedParameterName($alarm)
    {
        $machineParamName = $alarm->listData->machineParameter->name ?? '';
        $positionName = $alarm->listData->position->name ?? '';
        $datvarName = $alarm->listData->datvar->name ?? '';

        if (
            strtoupper($machineParamName) === strtoupper($positionName)
        ) {
            return $datvarName;
        }

        return implode(' - ', array_filter([
            $machineParamName ?: 'N/A',
            $positionName ?: 'N/A',
            $datvarName ?: 'N/A'
        ]));
    }

    public function getAlarmReason($alarm)
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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
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
            $query->whereHas('listData.datvar', function ($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%']);
            });
        }

        if ($this->alertTypeFilter) {
            $query->where('alert_type', $this->alertTypeFilter);
        }

        // Apply sorting
        switch ($this->sortField) {
            case 'parameter':
                $query->whereHas('listData.machineParameter', function ($q) {
                    $q->orderBy('name', $this->sortDirection);
                });
                break;
            case 'alert_type':
                $query->orderBy('alert_type', $this->sortDirection);
                break;
            case 'start_time':
                $query->orderBy('start_time', $this->sortDirection);
                break;
            case 'end_time':
                $query->orderBy('end_time', $this->sortDirection);
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $alarms = $query->paginate(10);

        return view('livewire.current-alarm-table', [
            'alarms' => $alarms
        ]);
    }
}
