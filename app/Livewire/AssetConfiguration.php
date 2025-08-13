<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\ListData;
use Livewire\Component;

class AssetConfiguration extends Component
{
    public $assetId = null;
    public $parameters = [];
    public $search = '';

    protected $listeners = [
        'assetSelected' => 'setAsset',
        'refreshParameters' => 'loadParameters'
    ];

    public function mount($assetId = null)
    {
        $this->assetId = $assetId;
        if ($assetId) {
            $this->loadParameters();
        }
    }

    public function setAsset($assetId)
    {
        $this->assetId = $assetId;
        $this->loadParameters();
    }

    public function getFormattedParameterName($parameter)
    {
        if (
            strtoupper($parameter->machineParameter->name ?? '') === strtoupper($parameter->position->name ?? '')
        ) {
            return $parameter->datvar->name;
        }

        return ($parameter->machineParameter->name ?? 'N/A') . ' - ' .
            ($parameter->position->name ?? 'N/A') . ' - ' .
            ($parameter->datvar->name ?? 'N/A');
    }

    public function getFilteredParameters()
    {
        return collect($this->parameters)->filter(function ($parameter) {
            $searchTerm = strtolower($this->search);
            $parameterName = strtolower($this->getFormattedParameterName($parameter));

            return empty($this->search) || str_contains($parameterName, $searchTerm);
        });
    }

    public function loadParameters()
    {
        if (!$this->assetId) {
            $this->parameters = [];
            return;
        }

        $this->parameters = ListData::where('asset_id', $this->assetId)
            ->with(['machineParameter', 'position', 'datvar'])
            ->get();
    }

    public function openEditModal($parameterId)
    {
        $parameter = collect($this->parameters)->firstWhere('id', $parameterId);
        if ($parameter) {
            $parameterName = $this->getFormattedParameterName($parameter);

            $this->dispatch(
                'openParameterLimitModal',
                parameterId: $parameterId,
                parameterName: $parameterName,
                warningLimit: $parameter->warning_limit,
                dangerLimit: $parameter->danger_limit,
                unit: $parameter->datvar->unit ?? ''
            );
        }
    }

    public function render()
    {
        return view('livewire.asset-configuration', [
            'filteredParameters' => $this->getFilteredParameters()
        ]);
    }
}
