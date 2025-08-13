<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\ListData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AssetDangerCount extends Component
{
    public $assetDangers;
    public $totalDangerAssets = 0;

    protected $listeners = ['refreshAssetDangerCount' => 'getAssetDangerData'];

    public function mount()
    {
        $this->getAssetDangerData();
    }

    public function getAssetDangerData()
    {
        // Get all assets with their listData
        $assets = Asset::with('listData')->get();
        $this->assetDangers = [];
        $this->totalDangerAssets = 0;

        foreach ($assets as $asset) {
            // Count warning and danger parameters for the asset
            $warningCount = $asset->listData->where('condition', 'warning')->count();
            $dangerCount = $asset->listData->where('condition', 'danger')->count();

            // Asset is in danger state if it has any danger parameters or more than 3 warning parameters
            if ($dangerCount > 0 || $warningCount > 3) {
                $this->assetDangers[] = [
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                    'danger_count' => $dangerCount + ($warningCount > 3 ? 1 : 0)
                ];
                $this->totalDangerAssets++;
            }
        }
    }

    public function render()
    {
        return view('livewire.asset-danger-count', [
            'totalDangerAssets' => $this->totalDangerAssets
        ]);
    }
}
