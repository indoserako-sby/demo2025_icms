<div>
    <style>
        .accordion-button::after {
            display: none !important;
        }
    </style>

    <!-- Tambahkan input pencarian di dalam komponen Livewire -->
    <div class="mb-3">
        <input type="text" class="form-control" placeholder="Search asset..." wire:model.live="search">
    </div>

    <div class="asset-tree-container" style="max-height: 600px; overflow-y: auto;">
        @forelse ($areas as $area)
            <div class="accordion mb-1">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button wire:click="toggleArea({{ $area->id }})"
                            class="accordion-button d-flex {{ !in_array($area->id, $expandedAreas) ? 'collapsed' : '' }}"
                            type="button">
                            <i
                                class="ti {{ in_array($area->id, $expandedAreas) ? 'ti-chevron-down' : 'ti-chevron-right' }} me-2"></i>
                            <i class="ti ti-building-factory me-2"></i>{{ $area->name }}
                        </button>
                    </h2>

                    <div class="accordion-collapse collapse {{ in_array($area->id, $expandedAreas) ? 'show' : '' }}">
                        <div class="accordion-body p-0">
                            <!-- Groups within this area -->
                            @forelse ($area->groups as $group)
                                <div class="ms-3 mb-2">
                                    <div class="d-flex align-items-center bg-lighter p-2 rounded cursor-pointer"
                                        wire:click="toggleGroup({{ $group->id }})">
                                        <i
                                            class="ti {{ in_array($group->id, $expandedGroups) ? 'ti-chevron-down' : 'ti-chevron-right' }} me-2"></i>
                                        <span><i class="ti ti-server me-2"></i>{{ $group->name }}</span>
                                    </div>

                                    <!-- Assets within this group -->
                                    @if (in_array($group->id, $expandedGroups))
                                        <div class="ms-4 mt-1">
                                            @forelse ($group->assets as $asset)
                                                <div class="d-flex align-items-center p-1 rounded {{ $selectedAssetId == $asset->id ? 'bg-primary text-white' : 'hover-bg-light' }} cursor-pointer"
                                                    wire:click="selectAsset({{ $asset->id }})">
                                                    <i class="ti ti-device me-2"></i>{{ $asset->name }}
                                                </div>
                                            @empty
                                                <div class="text-muted small ps-2 py-1">No assets in this group</div>
                                            @endforelse
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-muted small p-3">No groups in this area</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                No assets found. {{ !empty($search) ? 'Try a different search term.' : '' }}
            </div>
        @endforelse
    </div>
</div>
