<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;

class GroupCount extends Component
{
    public function render()
    {
        $groupCount = Group::count();
        return view('livewire.group-count', [
            'groupCount' => $groupCount
        ]);
    }
}
