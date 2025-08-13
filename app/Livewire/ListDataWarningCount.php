<?php

namespace App\Livewire;

use App\Models\ListData;
use Livewire\Component;

class ListDataWarningCount extends Component
{
    public $count;

    protected $listeners = ['refreshListDataWarningCount' => 'getWarningCount'];

    public function mount()
    {
        $this->getWarningCount();
    }

    public function getWarningCount()
    {
        $this->count = ListData::where('condition', 'warning')->count();
    }

    public function render()
    {
        return view('livewire.list-data-warning-count');
    }
}
