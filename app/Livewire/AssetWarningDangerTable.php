<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\ListData;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AssetWarningDangerTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshAssetWarningDangerTable' => '$refresh'];

    public function render()
    {
        // Get assets with warning or danger conditions
        $assets = Asset::whereHas('listData', function ($query) {
            $query->where('condition', 'warning')
                ->orWhere('condition', 'danger');
        })
            ->withCount([
                'listData as warning_count' => function ($query) {
                    $query->where('condition', 'warning');
                },
                'listData as danger_count' => function ($query) {
                    $query->where('condition', 'danger');
                }
            ])
            ->orderByRaw('danger_count DESC, warning_count DESC')
            ->paginate(10);

        // Transform the collection
        $assets->getCollection()->transform(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'condition' => $asset->danger_count > 0 ? 'danger' : 'warning'
            ];
        });

        return view('livewire.asset-warning-danger-table', [
            'paginatedAssets' => $assets
        ]);
    }
}
