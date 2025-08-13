<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DataAlarm;
use App\Models\Asset;
use App\Models\ListData;
use Livewire\WithPagination;
use App\Models\MachineParameter;
use App\Exports\HistoricalAlarmsExport;
use Maatwebsite\Excel\Facades\Excel;

class HistoricalAlarmTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $assetId = null;
    public $asset = null;
    public $parameter = '';
    public $alertType = '';
    public $alarmCause = '';
    public $acknowledgePerson = '';
    public $startDate = '';
    public $endDate = '';
    public $searchTerm = '';

    protected $listeners = ['assetSelected' => 'loadAsset'];

    protected $queryString = [
        'parameter' => ['except' => ''],
        'alertType' => ['except' => ''],
        'alarmCause' => ['except' => ''],
        'acknowledgePerson' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];
    public function mount()
    {
        // Set default date range to last 7 days if not specified
        if (empty($this->startDate)) {
            $this->startDate = now()->subDays(7)->format('Y-m-d');
        }
        if (empty($this->endDate)) {
            $this->endDate = now()->format('Y-m-d');
        }
    }

    public function loadAsset($assetId)
    {
        $this->assetId = $assetId;
        $this->asset = Asset::find($assetId);
        $this->resetPage();
    }

    public function updatingParameter()
    {
        $this->resetPage();
    }

    public function updatingAlertType()
    {
        $this->resetPage();
    }

    public function updatingAlarmCause()
    {
        $this->resetPage();
    }
    public function updatingAcknowledgePerson()
    {
        $this->resetPage();
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

    public function exportToExcel()
    {
        if (!$this->assetId) {
            session()->flash('error', 'Please select an asset before exporting');
            return;
        }

        $assetName = $this->asset ? $this->asset->name : 'Selected Asset';
        $fileName = 'historical_alarms_' . $assetName . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new HistoricalAlarmsExport(
                $this->assetId,
                $this->parameter,
                $this->alertType,
                $this->alarmCause,
                $this->acknowledgePerson,
                $this->startDate,
                $this->endDate,
                $assetName
            ),
            $fileName
        );
    }

    public function render()
    {
        if (!$this->assetId) {
            return view('livewire.historical-alarm-table', [
                'alarms' => collect(),
                'parameters' => collect()
            ]);
        }

        $parameters = ListData::where('asset_id', $this->assetId)
            ->with(['machineParameter', 'position', 'datvar'])
            ->get()
            ->map(function ($listData) {
                $machineParamName = $listData->machineParameter->name ?? '';
                $positionName = $listData->position->name ?? '';
                $datvarName = $listData->datvar->name ?? '';

                $formattedName = $machineParamName;

                if (!(strtoupper($machineParamName) === strtoupper($positionName))) {
                    $formattedName = implode(' - ', array_filter([
                        $machineParamName ?: 'N/A',
                        $positionName ?: 'N/A',
                        $datvarName ?: 'N/A'
                    ]));
                }

                return [
                    'id' => $listData->id,
                    'name' => $formattedName
                ];
            })
            ->unique('name')
            ->values();

        $query = DataAlarm::query()
            ->with(['listData.machineParameter', 'listData.position', 'listData.datvar', 'acknowledgedByUser'])
            ->whereHas('listData', function ($query) {
                $query->where('asset_id', $this->assetId);
            })
            ->where('acknowledged', true)
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

        $alarms = $query->paginate(10);

        return view('livewire.historical-alarm-table', [
            'alarms' => $alarms,
            'parameters' => $parameters,
        ]);
    }
}
