<?php

namespace App\Livewire;

use App\Models\ListData;
use App\Models\LogData;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AssetAnalysisParameters extends Component
{
    public $assetId = null;
    public $parameters = [];
    public $selectedParameters = [];
    public $selectAll = false;
    public $previousSelectedParameters = [];

    protected $listeners = [
        'assetSelected' => 'setAsset',
        'refreshParameters' => 'loadParameters',
        'closeModal' => 'handleModalClosed'
    ];

    public function boot() {}

    public function mount($assetId = null)
    {
        $this->assetId = $assetId;
    }

    public function hydrate() {}

    public function setAsset($assetId)
    {
        $previousAssetId = $this->assetId;
        if ($previousAssetId !== null) {
            // Store current parameters before changing asset
            $this->previousSelectedParameters = collect($this->parameters)
                ->whereIn('id', $this->selectedParameters)
                ->map(function ($param) {
                    return [
                        'machine_parameter_id' => $param->machineParameter->id ?? null,
                        'position_id' => $param->position->id ?? null,
                        'datvar_id' => $param->datvar->id ?? null
                    ];
                })->toArray();
        }

        $this->assetId = $assetId;
        $this->selectedParameters = [];
        $this->selectAll = false;
        $this->loadParameters();

        // If we have previous parameters, try to match them
        if (!empty($this->previousSelectedParameters)) {
            $this->matchPreviousParameters();
        }
    }

    private function matchPreviousParameters()
    {
        $newSelectedParameters = [];

        foreach ($this->parameters as $parameter) {
            foreach ($this->previousSelectedParameters as $prevParam) {
                if (
                    ($parameter->machineParameter->id ?? null) === $prevParam['machine_parameter_id'] &&
                    ($parameter->position->id ?? null) === $prevParam['position_id'] &&
                    ($parameter->datvar->id ?? null) === $prevParam['datvar_id']
                ) {
                    $newSelectedParameters[] = $parameter->id;
                    break;
                }
            }
        }

        if (!empty($newSelectedParameters)) {
            $this->selectedParameters = $newSelectedParameters;
            $this->selectAll = count($this->selectedParameters) === count($this->parameters);
            $this->emitParametersChange();

            // Update chart
            $this->dispatch('custom-parameters-selected', [
                'parameters' => $this->selectedParameters,
                'immediate' => true
            ]);
        }
    }

    public function loadParameters()
    {
        if (!$this->assetId) {
            $this->parameters = [];
            return;
        }

        // Get the parameters for the asset with their related data
        $parameters = ListData::where('asset_id', $this->assetId)
            ->with(['machineParameter', 'position', 'datvar'])
            ->orderBy('id', 'asc')
            ->get()
            ->sortBy(function ($data) {
                $mpName = strtoupper($data->machineParameter->name ?? '');
                $posName = strtoupper($data->position->name ?? '');
                $datvarName = strtoupper($data->datvar->name ?? '');
                if ($mpName === $posName) {
                    return $datvarName;
                }
                return $mpName . ' - ' . $posName . ' - ' . $datvarName;
            });

        // Try to get most recent log data for each parameter
        $latestLogs = $this->getLatestLogData($this->assetId);

        // Process each parameter
        foreach ($parameters as $parameter) {
            // Check if we have log data for this parameter
            if (isset($latestLogs[$parameter->id])) {
                $parameter->current_value = $latestLogs[$parameter->id]->value ?? 0;
                $parameter->unit = $latestLogs[$parameter->id]->unit ?? $parameter->datvar->unit ?? '°C';
            } else {
                // If no log data exists, use the value directly from list_data
                // Handle null values by setting them to 0
                $parameter->current_value = $parameter->value ?? 0;
                $parameter->unit = $parameter->datvar->unit ?? '°C'; // Default unit
            }

            // Make sure warning_limit and danger_limit are not null
            $parameter->warning_limit = $parameter->warning_limit ?? 0;
            $parameter->danger_limit = $parameter->danger_limit ?? 0;
        }

        $this->parameters = $parameters;
    }

    /**
     * Get the latest log data for all parameters of an asset
     */
    private function getLatestLogData($assetId)
    {
        // Get the latest log date for each parameter (list_data_id)
        $latestLogDates = LogData::where('asset_id', $assetId)
            ->select('list_data_id', DB::raw('MAX(date) as max_date'))
            ->groupBy('list_data_id')
            ->pluck('max_date', 'list_data_id');

        // If we don't have any log data, return empty array
        if ($latestLogDates->isEmpty()) {
            return [];
        }

        // Get the actual log entries for these dates
        $latestLogs = [];
        foreach ($latestLogDates as $listDataId => $maxDate) {
            $log = LogData::where('asset_id', $assetId)
                ->where('list_data_id', $listDataId)
                ->where('date', $maxDate)
                ->latest('created_at')  // In case there are multiple entries on the same date
                ->first();

            if ($log) {
                $latestLogs[$listDataId] = $log;
            }
        }

        return $latestLogs;
    }

    public function updatedSelectAll()
    {

        // Set selected parameters berdasarkan status selectAll
        if ($this->selectAll) {
            $this->selectedParameters = collect($this->parameters)->pluck('id')->toArray();
        } else {
            $this->selectedParameters = [];
        }


        // First, emit the parameters change event
        $this->emitParametersChange();

        // Then dispatch browser event for direct sync
        $this->dispatch('parameters-updated', [
            'parameters' => $this->selectedParameters
        ]);

        // Finally dispatch custom event for chart update
        $this->dispatch('custom-parameters-selected', [
            'parameters' => $this->selectedParameters,
            'immediate' => true
        ]);
    }

    public function updatedSelectedParameters()
    {
        $this->selectAll = count($this->selectedParameters) === count($this->parameters);
        $this->emitParametersChange();

        // Add JavaScript to update the hidden input for direct sync
        $this->dispatch('browser-event', [
            'name' => 'parameters-updated',
            'detail' => ['parameters' => $this->selectedParameters]
        ]);
    }

    public function emitParametersChange()
    {
        // Pastikan data yang dikirim berupa array sederhana (bukan nested array)
        $cleanParameters = [];

        // Bersihkan array dari struktur Livewire khusus
        foreach ($this->selectedParameters as $param) {
            if (is_array($param)) {
                if (count($param) > 0) {
                    $cleanParameters[] = (int)$param[0]; // Ambil nilai pertama jika array
                }
            } else {
                $cleanParameters[] = (int)$param; // Konversi ke integer
            }
        }

        // Log untuk debugging

        // 1. Standar Livewire 3 dispatch
        $this->dispatch('parametersSelected', $cleanParameters);

        // 2. Targetkan komponen spesifik
        $this->dispatch('parametersSelected', $cleanParameters)->to('asset-analysis-chart');

        // 3. METODE BARU - Dispatch ke semua komponen
        $this->dispatch('parametersSelected', $cleanParameters)->self()->to('asset-analysis-chart');

        // 4. ALTERNATIF - menggunakan browser event (Livewire v3 syntax)
        $this->dispatch('custom-parameters-selected', ['parameters' => $cleanParameters]);

        // 5. DIRECT PROPERTY UPDATE - coba update properti komponen chart langsung
        try {
            $chartComponentClass = app()->make(\App\Livewire\AssetAnalysisChart::class);
            if ($chartComponentClass) {

                // Gunakan dispatch untuk Livewire v3
                if (class_exists('Livewire\Livewire')) {
                    // Gunakan dispatch yang tersedia di Livewire v3
                    $this->dispatch('parametersSelected', $cleanParameters);
                }
            }
        } catch (\Exception $e) {
        }
    }

    public function openEditModal($parameterId)
    {
        // Find the parameter
        $parameter = collect($this->parameters)->firstWhere('id', $parameterId);
        if ($parameter) {
            // Format parameter name based on the same logic
            $parameterName = '';
            if (
                strtoupper($parameter->machineParameter->name ?? '') === strtoupper($parameter->position->name ?? '')
            ) {
                $parameterName = $parameter->datvar->name;
            } else {
                $parameterName = ($parameter->machineParameter->name ?? 'N/A') . ' - ' .
                    ($parameter->position->name ?? 'N/A') . ' - ' .
                    ($parameter->datvar->name ?? 'N/A');
            }

            $this->dispatch(
                'openParameterLimitModal',
                parameterId: $parameterId,
                parameterName: $parameterName,
                warningLimit: $parameter->warning_limit,
                dangerLimit: $parameter->danger_limit,
                unit: $parameter->datvar->unit ?? '°C'
            );
        }
    }

    public function handleModalClosed()
    {
        // This method is called when the modal is closed via the hidden.bs.modal event
        // We can use it to refresh the parameters if needed
        $this->loadParameters();
    }

    public function render()
    {
        return view('livewire.asset-analysis-parameters');
    }
}
