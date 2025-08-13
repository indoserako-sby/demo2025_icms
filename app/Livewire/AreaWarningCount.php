<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Group;
use App\Models\Asset;
use App\Models\ListData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AreaWarningCount extends Component
{
    public $areaWarnings;
    public $totalWarningAreas = 0;

    protected $listeners = ['refreshAreaWarningCount' => 'getAreaWarningData'];

    public function mount()
    {
        $this->getAreaWarningData();
    }

    public function getAreaWarningData()
    {
        // Get all areas with their groups and assets
        $areas = Area::with(['groups.assets.listData'])->get();
        $this->areaWarnings = [];
        $this->totalWarningAreas = 0;

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

            // Only include in warning count if it has warning groups but not enough for danger status
            if ($warningGroups > 0 && $dangerGroups == 0 && $warningGroups <= 3) {
                $this->areaWarnings[] = [
                    'area_id' => $area->id,
                    'area_name' => $area->name,
                    'warning_count' => $warningGroups
                ];
                $this->totalWarningAreas++;
            }
        }
    }

    public function render()
    {
        return view('livewire.area-warning-count', [
            'totalWarningAreas' => $this->totalWarningAreas
        ]);
    }
}
