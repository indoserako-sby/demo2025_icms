<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\Asset;
use Livewire\Component;

class AssetAnalysisTree extends Component
{
    public $search = '';
    public $expandedAreas = [];
    public $expandedGroups = [];
    public $selectedAssetId = null;

    protected $listeners = [
        'refreshAssetTree' => '$refresh',
        'expandAndSelectAsset' => 'handleExpandAndSelectAsset'
    ];

    // Update search property dan secara otomatis buka dropdown
    public function updatedSearch()
    {
        if (!empty($this->search)) {
            // Reset expanded lists
            $this->expandedAreas = [];
            $this->expandedGroups = [];

            $searchTerm = strtolower($this->search);
            // Cari asset yang cocok dengan pencarian (case-insensitive)
            $matchedAssets = Asset::whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%'])->get();

            // Jika ada asset yang cocok, ambil area dan group IDs
            if ($matchedAssets->count() > 0) {
                foreach ($matchedAssets as $asset) {
                    // Tambahkan area dan group ke daftar yang dibuka
                    if ($asset->group && $asset->group->area) {
                        $this->expandedAreas[] = $asset->group->area_id;
                        $this->expandedGroups[] = $asset->group_id;
                    }
                }

                // Remove duplicates
                $this->expandedAreas = array_unique($this->expandedAreas);
                $this->expandedGroups = array_unique($this->expandedGroups);
            }
        }
    }

    public function toggleArea($areaId)
    {
        if (in_array($areaId, $this->expandedAreas)) {
            // Remove from expanded areas
            $this->expandedAreas = array_diff($this->expandedAreas, [$areaId]);
        } else {
            // Add to expanded areas
            $this->expandedAreas[] = $areaId;
        }
    }

    public function toggleGroup($groupId)
    {
        if (in_array($groupId, $this->expandedGroups)) {
            // Remove from expanded groups
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupId]);
        } else {
            // Add to expanded groups
            $this->expandedGroups[] = $groupId;
        }
    }

    public function selectAsset($assetId)
    {
        $this->selectedAssetId = $assetId;

        // Emit event to other components to update based on selected asset
        $this->dispatch('assetSelected', $assetId);
    }

    public function handleExpandAndSelectAsset($data)
    {
        // Extract data from the event
        $assetId = $data['asset_id'] ?? null;
        $groupId = $data['group_id'] ?? null;
        $areaId = $data['area_id'] ?? null;

        // Expand the area if it's valid
        if ($areaId && !in_array($areaId, $this->expandedAreas)) {
            $this->expandedAreas[] = $areaId;
        }

        // Expand the group if it's valid
        if ($groupId && !in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups[] = $groupId;
        }

        // Select the asset
        if ($assetId) {
            $this->selectedAssetId = $assetId;

            // Emit the assetSelected event to other components
            $this->dispatch('assetSelected', $assetId);
        }
    }

    public function render()
    {
        if (!empty($this->search)) {
            $searchTerm = strtolower($this->search);
            // First find matching assets (case-insensitive)
            $matchingAssetIds = Asset::whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%'])
                ->pluck('id');

            $areas = Area::whereHas('groups.assets', function ($query) use ($matchingAssetIds) {
                $query->whereIn('id', $matchingAssetIds);
            })
                ->with(['groups' => function ($groupQuery) use ($matchingAssetIds) {
                    // Only load groups that contain matching assets
                    $groupQuery->whereHas('assets', function ($assetQuery) use ($matchingAssetIds) {
                        $assetQuery->whereIn('id', $matchingAssetIds);
                    });
                    // Load the matching assets for these groups, ordered by id
                    $groupQuery->with(['assets' => function ($assetQuery) use ($matchingAssetIds) {
                        $assetQuery->whereIn('id', $matchingAssetIds)
                            ->orderBy('id');
                    }]);
                }])
                ->orderBy('id')
                ->get();
        } else {
            $areas = Area::with(['groups' => function ($groupQuery) {
                $groupQuery->with(['assets' => function ($assetQuery) {
                    $assetQuery->orderBy('id');
                }]);
            }])->orderBy('id')->get();
        }

        return view('livewire.asset-analysis-tree', [
            'areas' => $areas
        ]);
    }
}
