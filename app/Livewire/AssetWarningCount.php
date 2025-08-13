<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\ListData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AssetWarningCount extends Component
{
    public $assetWarnings;
    public $totalWarningAssets = 0;

    protected $listeners = ['refreshAssetWarningCount' => 'getAssetWarningData'];

    public function mount()
    {
        $this->getAssetWarningData();
    }

    public function getAssetWarningData()
    {
        // Get all assets with their listData
        $assets = Asset::with('listData')->get();
        $this->assetWarnings = [];
        $this->totalWarningAssets = 0;

        foreach ($assets as $asset) {
            // Count warning and danger parameters for the asset
            $warningCount = $asset->listData->where('condition', 'warning')->count();
            $dangerCount = $asset->listData->where('condition', 'danger')->count();

            // Asset is in warning state if it has 1-3 warning parameters and no danger parameters
            if ($warningCount > 0 && $warningCount <= 3 && $dangerCount == 0) {
                $this->assetWarnings[] = [
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                    'warning_count' => $warningCount
                ];
                $this->totalWarningAssets++;
            }
        }
    }

    public function render()
    {
        return view('livewire.asset-warning-count', [
            'totalWarningAssets' => $this->totalWarningAssets
        ]);
    }
}
