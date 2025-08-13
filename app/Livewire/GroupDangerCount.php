<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\Asset;
use App\Models\ListData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GroupDangerCount extends Component
{
    public $groupDangers;
    public $totalDangerGroups = 0;

    protected $listeners = ['refreshGroupDangerCount' => 'getGroupDangerData'];

    public function mount()
    {
        $this->getGroupDangerData();
    }

    public function getGroupDangerData()
    {
        // Get all groups with their assets
        $groups = Group::with(['assets.listData'])->get();
        $this->groupDangers = [];
        $this->totalDangerGroups = 0;

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

            // Include in danger count if it has enough for danger status
            if ($dangerAssets > 0 || $warningAssets > 3) {
                $this->groupDangers[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'danger_count' => $dangerAssets + ($warningAssets > 3 ? 1 : 0)
                ];
                $this->totalDangerGroups++;
            }
        }
    }

    public function render()
    {
        return view('livewire.group-danger-count', [
            'totalDangerGroups' => $this->totalDangerGroups
        ]);
    }
}
