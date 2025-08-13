<?php

namespace App\Livewire;

use App\Models\ListData;
use App\Models\MachineParameter;
use Livewire\Component;

class ParameterLimitModal extends Component
{
    public $showModal = false;
    public $parameterId;
    public $parameterName;
    public $warningLimit;
    public $dangerLimit;
    public $unit;

    protected $listeners = ['openParameterLimitModal' => 'openModal'];

    public function openModal($parameterId, $parameterName, $warningLimit, $dangerLimit)
    {
        $this->parameterId = $parameterId;
        $this->parameterName = $parameterName;
        $this->warningLimit = $warningLimit ?? 0;
        $this->dangerLimit = $dangerLimit ?? 0;

        // Get the parameter's unit from the machine_parameter record
        $listData = ListData::find($parameterId);
        if ($listData && $listData->machine_parameter_id) {
            $machineParameter = MachineParameter::find($listData->machine_parameter_id);
            $this->unit = $listData->datvar->unit ?? '';
        } else {
            $this->unit = '';
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['parameterId', 'parameterName', 'warningLimit', 'dangerLimit', 'unit']);

        // Emit an event to notify that the modal has been closed properly
        $this->dispatch('modalClosed');
    }

    public function updatedShowModal()
    {
        if (!$this->showModal) {
            $this->reset(['parameterId', 'parameterName', 'warningLimit', 'dangerLimit', 'unit']);
        }
    }

    public function updateLimits()
    {
        // Validate the inputs
        $this->validate([
            'warningLimit' => 'nullable|numeric',
            'dangerLimit' => 'nullable|numeric',
        ]);

        try {
            // Find the parameter and update its limits
            $parameter = ListData::findOrFail($this->parameterId);
            $parameter->update([
                'warning_limit' => $this->warningLimit ?? 0,
                'danger_limit' => $this->dangerLimit ?? 0,
            ]);

            // Use session flash instead of direct dispatch to prevent duplicates
            session()->flash('success', 'Parameter limits updated successfully');

            // Refresh parameters display
            $this->dispatch('refreshParameters');

            // Close the modal
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update parameter limits: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.parameter-limit-modal');
    }
}
