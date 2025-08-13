<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Group;
use App\Models\Asset;
use App\Models\ListData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AreaDangerCount extends Component
{
    public $areaDangers;
    public $totalDangerAreas = 0;

    protected $listeners = ['refreshAreaDangerCount' => 'getAreaDangerData'];

    public function mount()
    {
        $this->getAreaDangerData();
    }

    public function getAreaDangerData()
    {
        // Get all areas with their groups and assets
        $areas = Area::with(['groups.assets.listData'])->get();
        $this->areaDangers = [];
        $this->totalDangerAreas = 0;

        foreach ($areas as $area) {
            $warningGroups = 0;
            $dangerGroups = 0;

            foreach ($area->groups as $group) {
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

                // Check group status based on new rules
                if ($dangerAssets > 0 || $warningAssets > 3) {
                    $dangerGroups++;
                } elseif ($warningAssets > 0) {
                    $warningGroups++;
                }
            }

            // Include in danger count if it has enough for danger status
            if ($dangerGroups > 0 || $warningGroups > 3) {
                $this->areaDangers[] = [
                    'area_id' => $area->id,
                    'area_name' => $area->name,
                    'danger_count' => $dangerGroups + ($warningGroups > 3 ? 1 : 0)
                ];
                $this->totalDangerAreas++;
            }
        }
    }

    public function render()
    {
        return view('livewire.area-danger-count', [
            'totalDangerAreas' => $this->totalDangerAreas
        ]);
    }
}
