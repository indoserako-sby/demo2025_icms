<div>
    <!-- Search input -->
    <div class="mb-3">
        <input type="text" class="form-control" placeholder="Search asset..." wire:model.live="search">
    </div>

    <!-- Asset list container -->
    <div class="asset-list-container" style="max-height: 600px; overflow-y: auto;">
        @forelse ($assets as $asset)
            <div class="d-flex align-items-center p-2 rounded mb-1 border {{ $selectedAssetId == $asset->id ? 'bg-primary text-white' : 'hover-bg-light' }} cursor-pointer"
                wire:click="selectAsset({{ $asset->id }})">
                <i class="ti ti-device me-2"></i>
                {{ $asset->name }}
            </div>
        @empty
            <div class="alert alert-info">
                No assets found. {{ !empty($search) ? 'Try a different search term.' : '' }}
            </div>
        @endforelse
    </div>
</div>
