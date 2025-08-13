<?php

namespace App\Livewire;

use App\Models\ListData;
use Livewire\Component;

class ListDataCount extends Component
{
    public $count;

    protected $listeners = ['refreshListDataCount' => 'getListDataCount'];

    public function mount()
    {
        $this->getListDataCount();
    }

    public function getListDataCount()
    {
        $this->count = ListData::count();
    }

    public function render()
    {
        return view('livewire.list-data-count');
    }
}
