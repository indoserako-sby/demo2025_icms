<?php

namespace App\Livewire;

use App\Models\ListData;
use Livewire\Component;

class ListDataDangerCount extends Component
{
    public $count;

    protected $listeners = ['refreshListDataDangerCount' => 'getDangerCount'];

    public function mount()
    {
        $this->getDangerCount();
    }

    public function getDangerCount()
    {
        $this->count = ListData::where('condition', 'danger')->count();
    }

    public function render()
    {
        return view('livewire.list-data-danger-count');
    }
}
