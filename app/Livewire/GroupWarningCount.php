<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\Asset;
use App\Models\ListData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GroupWarningCount extends Component
{
    public $groupWarnings;
    public $totalWarningGroups = 0;

    protected $listeners = ['refreshGroupWarningCount' => 'getGroupWarningData'];

    public function mount()
    {
        $this->getGroupWarningData();
    }

    public function getGroupWarningData()
    {
        // Get all groups with their assets
        $groups = Group::with(['assets.listData'])->get();
        $this->groupWarnings = [];
        $this->totalWarningGroups = 0;

        foreach ($groups as $group) {
            $warningAssets = 0;
            $dangerAssets = 0;

            foreach ($group->assets as $asset) {
                // Count warning and danger parameters for the asset
                $warningCount = $asset->listData->where('condition', 'warning')->count();
                $dangerCount = $asset->listData->where('condition', 'danger')->count();

                // Check asset status based on new rules
                if ($dangerCount > 0 || $warningCount > 3) {
                    $dangerAssets++;
                } elseif ($warningCount > 0) {
                    $warningAssets++;
                }
            }

            // Only include in warning count if it has warning assets but not enough for danger status
            if ($warningAssets > 0 && $dangerAssets == 0 && $warningAssets <= 3) {
                $this->groupWarnings[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'warning_count' => $warningAssets
                ];
                $this->totalWarningGroups++;
            }
        }
    }

    public function render()
    {
        return view('livewire.group-warning-count', [
            'totalWarningGroups' => $this->totalWarningGroups
        ]);
    }
}
