<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Group;
use App\Models\Asset;
use Livewire\Component;

class AreaGroupSelector extends Component
{
    public $areas = [];
    public $groups = [];
    public $assets = [];

    public $selectedArea = null;
    public $selectedGroup = null;
    public $selectedAsset = null;
    public function mount()
    {
        $this->areas = Area::all();
        $this->groups = collect(); // Mulai dengan koleksi kosong sampai area dipilih
        $this->assets = collect(); // Mulai dengan koleksi kosong sampai group dipilih
    }

    public function updatedSelectedArea($value)
    {
        // Reset pilihan group dan asset saat area berubah
        $this->selectedGroup = null;
        $this->selectedAsset = null;

        // Reset collections
        $this->assets = collect();

        // Jika area dipilih, ambil group terkait
        if ($value) {
            $this->groups = Group::where('area_id', $value)->get();
        } else {
            $this->groups = collect();
        }

        // Emit event untuk memberitahu frontend bahwa groups telah diperbarui
        $this->dispatch('updatedGroups');
    }

    public function updatedSelectedGroup($value)
    {
        // Reset pilihan asset saat group berubah
        $this->selectedAsset = null;

        // Jika group dipilih, ambil asset terkait
        if ($value) {
            $this->assets = Asset::where('group_id', $value)->get();
        } else {
            $this->assets = collect();
        }

        // Emit event ketika group dipilih atau diubah
        $this->dispatch('updatedGroups');
    }

    public function updatedSelectedAsset($value)
    {
        // Emit event ketika asset dipilih atau diubah
        $this->dispatch('updatedGroups');
    }

    public function render()
    {
        return view('livewire.area-group-selector');
    }

    // Setelah update apapun, pastikan UI diperbarui dengan benar
    public function updated($property)
    {
        $this->dispatch('updatedGroups');
    }
}
