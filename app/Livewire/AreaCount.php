<?php

namespace App\Livewire;

use App\Models\Area;
use Livewire\Component;

class AreaCount extends Component
{
    public function render()
    {
        $areaCount = Area::count();
        return view('livewire.area-count', [
            'areaCount' => $areaCount
        ]);
    }
}
