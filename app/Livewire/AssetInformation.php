<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Component;
use Illuminate\Support\Facades\Request;

class AssetInformation extends Component
{
    public $asset = null;
    public $assetId = null;

    protected $listeners = ['assetSelected' => 'loadAsset'];

    public function mount()
    {
        // Check if asset_id is passed in the URL
        $assetIdFromUrl = request()->query('asset_id');
        if ($assetIdFromUrl) {
            // Load asset with its related group and area
            $this->asset = Asset::with(['group.area'])->find($assetIdFromUrl);
            $this->assetId = $assetIdFromUrl;

            if ($this->asset && $this->asset->group && $this->asset->group->area) {
                $groupId = $this->asset->group_id;
                $areaId = $this->asset->group->area_id;

                // Dispatch event to update the tree view with all necessary info
                $this->dispatch('expandAndSelectAsset', [
                    'asset_id' => $this->assetId,
                    'group_id' => $groupId,
                    'area_id' => $areaId
                ]);
            }
        }
    }

    public function loadAsset($assetId)
    {
        $this->assetId = $assetId;
        $this->asset = Asset::with(['group.area'])->find($assetId);
    }

    public function render()
    {
        return view('livewire.asset-information');
    }
}
