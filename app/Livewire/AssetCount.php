<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Component;

class AssetCount extends Component
{
    public function render()
    {
        $assetCount = Asset::count();
        return view('livewire.asset-count', [
            'assetCount' => $assetCount
        ]);
    }
}
