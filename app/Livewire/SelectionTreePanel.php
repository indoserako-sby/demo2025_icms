<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Asset;
use App\Models\ListData;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class SelectionTreePanel extends Component
{
    public $areas = [];
    public $search = '';

    public $expandedAreas = [];
    public $expandedGroups = [];
    public $expandedAssets = [];

    public $selectedAssetId = null;
    public $selectedParameters = [];
    public $parameters = [];

    // Track the number of selected items
    public $selectedCount = 0;

    // Tracking selected parameters by entity
    public $assetSelectionCount = [];
    public $groupSelectionCount = [];
    public $areaSelectionCount = [];

    protected $listeners = [
        'refreshTree' => '$refresh'
    ];

    public function mount()
    {
        // Initialize empty arrays
        $this->selectedParameters = [];
        $this->assetSelectionCount = [];
        $this->groupSelectionCount = [];
        $this->areaSelectionCount = [];

        $this->loadAreas();
    }

    public function loadAreas()
    {
        if (!empty($this->search)) {
            // First find matching assets
            $matchingAssetIds = Asset::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%'])
                ->pluck('id');

            // Then load only areas that have these assets
            $this->areas = Area::whereHas('groups.assets', function ($query) use ($matchingAssetIds) {
                $query->whereIn('assets.id', $matchingAssetIds);
            })
                ->with(['groups' => function ($groupQuery) use ($matchingAssetIds) {
                    // Only load groups that contain matching assets
                    $groupQuery->whereHas('assets', function ($assetQuery) use ($matchingAssetIds) {
                        $assetQuery->whereIn('assets.id', $matchingAssetIds);
                    });
                    // Load the matching assets for these groups
                    $groupQuery->with(['assets' => function ($assetQuery) use ($matchingAssetIds) {
                        $assetQuery->whereIn('assets.id', $matchingAssetIds);
                    }]);
                }])
                ->orderBy('name')
                ->get();
        } else {
            // When not searching, load all areas with their groups and assets
            $this->areas = Area::with(['groups.assets'])->orderBy('id')->get();
        }
    }

    public function updatedSearch()
    {
        // Reset expanded sections when searching
        if (!empty($this->search)) {
            // Find matching assets and expand their parent areas/groups
            $matchingAssets = Asset::where('name', 'like', '%' . $this->search . '%')->get();

            foreach ($matchingAssets as $asset) {
                if ($asset->group) {
                    $this->expandedGroups[] = $asset->group->id;
                    if ($asset->group->area) {
                        $this->expandedAreas[] = $asset->group->area_id;
                    }
                }
            }

            // Remove duplicates
            $this->expandedAreas = array_unique($this->expandedAreas);
            $this->expandedGroups = array_unique($this->expandedGroups);
        }

        // Reload areas with the search filter
        $this->loadAreas();
    }

    public function toggleArea($areaId)
    {
        if (in_array($areaId, $this->expandedAreas)) {
            $this->expandedAreas = array_diff($this->expandedAreas, [$areaId]);
        } else {
            $this->expandedAreas[] = $areaId;
        }
    }

    public function toggleGroup($groupId)
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupId]);
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    public function toggleAsset($assetId)
    {
        $this->selectedAssetId = $assetId;

        if (in_array($assetId, $this->expandedAssets)) {
            $this->expandedAssets = array_diff($this->expandedAssets, [$assetId]);
        } else {
            $this->expandedAssets[] = $assetId;
            $this->loadAssetParameters($assetId);
        }
    }

    public function loadAssetParameters($assetId)
    {
        $this->parameters = ListData::where('asset_id', $assetId)->orderBy('id')
            ->with(['asset.group.area', 'machineParameter', 'position', 'datvar'])
            ->get()
            ->map(function ($item) {
                // Create a display name based on the requirement
                $machineName = $item->machineParameter->name ?? '';
                $positionName = $item->position->name ?? '';
                $datvarName = $item->datvar->name ?? '';
                $datavarUnit = $item->datvar->unit ?? '';

                // Store area and group IDs
                $this->groupSelectionCount[$item->asset->group_id] = $this->groupSelectionCount[$item->asset->group_id] ?? 0;
                $this->areaSelectionCount[$item->asset->group->area_id] = $this->areaSelectionCount[$item->asset->group->area_id] ?? 0;

                // Check if uppercase names are the same, if so just show machineParameter
                if (strtoupper($machineName) === strtoupper($positionName)) {
                    $displayName = $datvarName . ' (' . $datavarUnit . ')';
                } else {
                    $displayName = $machineName . ' ' . $positionName . ' ' . $datvarName . ' (' . $datavarUnit . ')';
                }

                return [
                    'id' => $item->id,
                    'name' => $displayName,
                    'machine_parameter_id' => $item->machine_parameter_id,
                    'position_id' => $item->position_id,
                    'datvar_id' => $item->datvar_id,
                    'asset_id' => $item->asset_id,
                    'group_id' => $item->asset->group_id,
                    'area_id' => $item->asset->group->area_id
                ];
            });
    }

    public function toggleParameter($parameterId)
    {
        // Ensure parameterId is an integer
        $parameterId = (int)$parameterId;

        // Find parameter in the flattened array
        $index = array_search($parameterId, array_map('intval', $this->selectedParameters));
        $listData = ListData::with(['asset.group.area'])->find($parameterId);

        if (!$listData) return;

        $assetId = $listData->asset_id;
        $groupId = $listData->asset->group_id;
        $areaId = $listData->asset->group->area_id;

        if ($index !== false) {
            // Remove parameter if already selected
            unset($this->selectedParameters[$index]);
            // Re-index array and ensure all values are integers
            $this->selectedParameters = array_values(array_map('intval', $this->selectedParameters));

            // Update counts
            if (isset($this->assetSelectionCount[$assetId])) {
                $this->assetSelectionCount[$assetId]--;
                if ($this->assetSelectionCount[$assetId] <= 0) {
                    unset($this->assetSelectionCount[$assetId]);
                }
            }

            if (isset($this->groupSelectionCount[$groupId])) {
                $this->groupSelectionCount[$groupId]--;
                if ($this->groupSelectionCount[$groupId] <= 0) {
                    unset($this->groupSelectionCount[$groupId]);
                }
            }

            if (isset($this->areaSelectionCount[$areaId])) {
                $this->areaSelectionCount[$areaId]--;
                if ($this->areaSelectionCount[$areaId] <= 0) {
                    unset($this->areaSelectionCount[$areaId]);
                }
            }
        } else {
            // Add parameter to selection
            $this->selectedParameters[] = $parameterId;

            // Update counts
            if (!isset($this->assetSelectionCount[$assetId])) {
                $this->assetSelectionCount[$assetId] = 0;
            }
            $this->assetSelectionCount[$assetId]++;

            if (!isset($this->groupSelectionCount[$groupId])) {
                $this->groupSelectionCount[$groupId] = 0;
            }
            $this->groupSelectionCount[$groupId]++;

            if (!isset($this->areaSelectionCount[$areaId])) {
                $this->areaSelectionCount[$areaId] = 0;
            }
            $this->areaSelectionCount[$areaId]++;
        }

        $this->selectedCount = count($this->selectedParameters);

        // Ensure selectedParameters is always a flat array of integers
        $this->selectedParameters = array_values(array_map('intval', $this->selectedParameters));

        // Dispatch parameter selection event so other components can respond
        $this->dispatch('parameterSelectionChanged', $this->selectedParameters);
    }

    public function render()
    {
        return view('livewire.selection-tree-panel');
    }
}
