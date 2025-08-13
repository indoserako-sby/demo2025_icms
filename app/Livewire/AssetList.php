<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Component;

class AssetList extends Component
{
    public $search = '';
    public $selectedAssetId = null;

    protected $listeners = ['refreshAssetList' => '$refresh'];

    public function updatedSearch()
    {
        // Reset selection when searching
        $this->selectedAssetId = null;
    }

    public function selectAsset($assetId)
    {
        $this->selectedAssetId = $assetId;
        $this->dispatch('assetSelected', $assetId);
    }

    public function render()
    {
        $assets = Asset::whereRaw('LOWER(name) like ?', ['%' . strtolower($this->search) . '%'])
            ->orderBy('name')
            ->get();

        return view('livewire.asset-list', [
            'assets' => $assets
        ]);
    }
}
