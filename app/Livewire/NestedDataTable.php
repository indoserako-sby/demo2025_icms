<?php

namespace App\Livewire;

use App\Models\ListData;
use App\Models\Area;
use App\Models\Group;
use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class NestedDataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $expandedAreas = [];
    public $expandedGroups = [];
    public $expandedAssets = [];

    protected $queryString = ['search'];

    public function expandArea($areaId)
    {
        if (in_array($areaId, $this->expandedAreas)) {
            $this->expandedAreas = array_diff($this->expandedAreas, [$areaId]);
        } else {
            $this->expandedAreas[] = $areaId;
        }
    }

    public function expandGroup($groupId)
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupId]);
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    public function expandAsset($assetId)
    {
        if (in_array($assetId, $this->expandedAssets)) {
            $this->expandedAssets = array_diff($this->expandedAssets, [$assetId]);
        } else {
            $this->expandedAssets[] = $assetId;
        }
    }

    public function getAreaData()
    {
        // Get areas with their relationships
        $areas = Area::with([
            'groups' => function ($query) {
                $query->orderBy('id', 'asc');
            },
            'groups.assets' => function ($query) {
                $query->orderBy('id', 'asc');
            }
        ])
            ->withCount(['groups'])
            ->orderBy('id', 'asc');

        if ($this->search) {
            $searchTerm = strtolower($this->search);
            $areas->where(function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%'])
                    ->orWhereHas('groups', function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%'])
                            ->orWhereHas('assets', function ($aq) use ($searchTerm) {
                                $aq->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                            });
                    });
            });
        }

        return $areas->get();
    }

    public function render()
    {
        // Get base areas with their relationships - apply search only at the parent levels
        $areasQuery = Area::with([
            'groups' => function ($query) {
                $query->orderBy('id', 'asc');
            },
            'groups.assets' => function ($query) {
                $query->orderBy('id', 'asc');
            }
        ])->withCount(['groups'])->orderBy('id', 'asc');

        if ($this->search) {
            $searchTerm = strtolower($this->search);
            $areasQuery->where(function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%'])
                    ->orWhereHas('groups', function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%'])
                            ->orWhereHas('assets', function ($aq) use ($searchTerm) {
                                $aq->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                            });
                    });
            });
        }

        $areas = $areasQuery->get();

        // Process condition counts at each level
        foreach ($areas as $area) {
            // Initialize area level counts
            $area->good_count = 0;
            $area->warning_count = 0;
            $area->danger_count = 0;

            foreach ($area->groups as $group) {
                // Initialize group level counts
                $group->good_count = 0;
                $group->warning_count = 0;
                $group->danger_count = 0;

                foreach ($group->assets as $asset) {
                    // Important: Always load all detailed data for an asset
                    // regardless of search filter - this ensures data is shown when expanded
                    $listDataQuery = ListData::where('asset_id', $asset->id)
                        ->with(['machineParameter', 'position', 'datvar'])
                        ->orderBy('id', 'asc');

                    // Only apply search filter to the detailed data if we're explicitly
                    // searching for parameter-level data
                    $detailedSearchApplied = false;
                    if ($this->search) {
                        $searchTerm = strtolower($this->search);
                        // Check if we need to apply detailed filter
                        $parameterCount = ListData::where('asset_id', $asset->id)
                            ->where(function ($query) use ($searchTerm) {
                                $query->whereHas('machineParameter', function ($q) use ($searchTerm) {
                                    $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                                })
                                    ->orWhereHas('position', function ($q) use ($searchTerm) {
                                        $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                                    })
                                    ->orWhereHas('datvar', function ($q) use ($searchTerm) {
                                        $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                                    });
                            })->count();

                        if ($parameterCount > 0) {
                            $listDataQuery->where(function ($query) use ($searchTerm) {
                                $query->whereHas('machineParameter', function ($q) use ($searchTerm) {
                                    $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                                })
                                    ->orWhereHas('position', function ($q) use ($searchTerm) {
                                        $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                                    })
                                    ->orWhereHas('datvar', function ($q) use ($searchTerm) {
                                        $q->whereRaw('LOWER(name) like ?', ['%' . $searchTerm . '%']);
                                    });
                            });
                            $detailedSearchApplied = true;
                        }
                    }

                    $listDataItems = $listDataQuery->get();

                    // If we didn't apply a detailed search, get all detailed data
                    if (!$detailedSearchApplied) {
                        $listDataItems = ListData::where('asset_id', $asset->id)
                            ->with(['machineParameter', 'position', 'datvar'])
                            ->get()
                            ->sortBy(function ($data) {
                                $mpName = strtoupper($data->machineParameter->name ?? '');
                                $posName = strtoupper($data->position->name ?? '');
                                $datvarName = strtoupper($data->datvar->name ?? '');
                                if ($mpName === $posName) {
                                    return $datvarName;
                                }
                                return $mpName . ' - ' . $posName . ' - ' . $datvarName;
                            })
                            ->values();
                    }

                    // Asset level counts (Level 3) - direct count of parameters
                    $asset->good_count = $listDataItems->where('condition', 'good')->count();
                    $asset->warning_count = $listDataItems->where('condition', 'warning')->count();
                    $asset->danger_count = $listDataItems->where('condition', 'danger')->count();

                    // For Level 2 (Group): Count assets with specific conditions
                    if ($asset->danger_count > 0) {
                        $group->danger_count++;
                    } else if ($asset->warning_count > 0) {
                        $group->warning_count++;
                    } else if ($asset->good_count > 0) {
                        $group->good_count++;
                    }

                    // Assign the data to detailed_data
                    $asset->detailed_data = $listDataItems;
                }

                // For Level 1 (Area): Count groups with specific conditions
                if ($group->danger_count > 0) {
                    $area->danger_count++;
                } else if ($group->warning_count > 0) {
                    $area->warning_count++;
                } else if ($group->good_count > 0) {
                    $area->good_count++;
                }
            }
        }

        return view('livewire.nested-data-table', [
            'areas' => $areas,
        ]);
    }
}
