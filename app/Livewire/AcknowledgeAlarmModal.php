<?php

namespace App\Livewire;

use App\Models\DataAlarm;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AcknowledgeAlarmModal extends Component
{
    public $showModal = false;
    public $alarmId = null;
    public $notes = '';
    public $alarmCause = '';
    public $machine_person = '';
    public $starttimemaintenance = '';
    public $endtimemaintenance = '';
    public $assetId = null;

    protected $listeners = ['openAcknowledgeModal' => 'open'];

    protected $rules = [
        'notes' => 'required|string',
        'alarmCause' => 'required|in:Fake Alarm,Mall Function,Test Alarm,Error Alarm',
        'machine_person' => 'required|string',
        'starttimemaintenance' => 'required|date_format:Y-m-d H:i',
        'endtimemaintenance' => 'required|date_format:Y-m-d H:i|after_or_equal:starttimemaintenance',
    ];

    public function mount($assetId = null)
    {
        $this->assetId = $assetId;
        $this->starttimemaintenance = now()->format('Y-m-d H:i');
        $this->endtimemaintenance = now()->format('Y-m-d H:i');
    }

    public function open($alarmId)
    {
        $this->alarmId = $alarmId;
        $this->reset(['notes', 'alarmCause', 'machine_person']);
        $this->starttimemaintenance = now()->format('Y-m-d H:i');
        $this->endtimemaintenance = now()->format('Y-m-d H:i');
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function close()
    {
        $this->showModal = false;
        $this->dispatch('closeModal');
    }

    public function acknowledgeAlarm()
    {
        $this->validate();

        $alarm = DataAlarm::find($this->alarmId);

        if ($alarm) {
            $alarm->update([
                'acknowledged' => true,
                'resolved' => true,
                'acknowledged_by' => Auth::id(),
                'acknowledged_at' => now(),
                'notes' => $this->notes,
                'alarm_cause' => $this->alarmCause,
                'machine_person' => $this->machine_person,
                'starttimemaintenance' => $this->starttimemaintenance,
                'endtimemaintenance' => $this->endtimemaintenance,
            ]);

            // Dispatch events to update alarm counts
            $this->dispatch('alarmAcknowledged', assetId: $this->assetId);
            $this->dispatch('refreshAlarmCount');
        }

        $this->close();
    }

    public function render()
    {
        return view('livewire.acknowledge-alarm-modal');
    }
}
