<?php

namespace App\Livewire;

use App\Models\DataAlarm;
use Livewire\Component;

class AlarmCount extends Component
{
    public $assetId = null;
    public $warningCount = 0;
    public $dangerCount = 0;

    protected $listeners = [
        'assetSelected' => 'loadAlarmCounts',
        'refreshAlarmCount' => 'refresh'
    ];

    public function loadAlarmCounts($assetId)
    {
        $this->assetId = $assetId;

        // Get the list_data_ids for the selected asset
        $listDataIds = \App\Models\ListData::where('asset_id', $assetId)
            ->pluck('id')
            ->toArray();

        // Set date range for last month
        $startDate = now()->subMonth()->startOfDay();
        $endDate = now()->endOfDay();

        // Count unacknowledged warning alarms
        $this->warningCount = DataAlarm::whereIn('list_data_id', $listDataIds)
            ->where('acknowledged', false)
            ->where('alert_type', 'warning')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Count unacknowledged danger alarms
        $this->dangerCount = DataAlarm::whereIn('list_data_id', $listDataIds)
            ->where('acknowledged', false)
            ->where('alert_type', 'danger')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();
    }

    public function refresh()
    {
        if ($this->assetId) {
            $this->loadAlarmCounts($this->assetId);
        }
    }

    public function render()
    {
        return view('livewire.alarm-count');
    }
}
