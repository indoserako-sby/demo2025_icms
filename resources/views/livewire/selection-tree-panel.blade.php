<div>

    <!-- Search Input -->
    <div class="mb-3">
        <input type="text" class="form-control" placeholder="Search assets..." wire:model.live.debounce.300ms="search">
    </div>

    <!-- Tree Structure for Selection -->
    <div class="asset-tree-container" style="max-height: 400px; overflow-y: auto;">
        @forelse ($areas as $area)
            <div class="accordion mb-1">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button wire:click="toggleArea({{ $area->id }})"
                            class="accordion-button {{ !in_array($area->id, $expandedAreas) ? 'collapsed' : '' }} p-0 bg-transparent shadow-none"
                            type="button" style="::after{display: none !important;}">
                            <div class="d-flex align-items-center w-100 p-2">
                                <i
                                    class="ti {{ in_array($area->id, $expandedAreas) ? 'ti-chevron-down' : 'ti-chevron-right' }} me-2"></i>
                                <i class="ti ti-building-factory me-2"></i>{{ $area->name }}
                                @if (isset($areaSelectionCount[$area->id]) && $areaSelectionCount[$area->id] > 0)
                                    <span
                                        class="badge bg-primary rounded-pill ms-auto">{{ $areaSelectionCount[$area->id] }}</span>
                                @endif
                            </div>
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
                                        @if (isset($groupSelectionCount[$group->id]) && $groupSelectionCount[$group->id] > 0)
                                            <span
                                                class="badge bg-primary rounded-pill ms-auto">{{ $groupSelectionCount[$group->id] }}</span>
                                        @endif
                                    </div>

                                    <!-- Assets within this group -->
                                    @if (in_array($group->id, $expandedGroups))
                                        <div class="ms-4 mt-1">

                                            {{-- aku ingin asset diurutkan berdasarkan nama
                                             --}}
                                            @php
                                                $group->assets = $group->assets->sortBy('name');
                                            @endphp
                                            {{-- Loop through assets in the group --}}
                                            @forelse ($group->assets as $asset)
                                                <div wire:key="asset-{{ $asset->id }}"
                                                    class="d-flex align-items-center p-1 rounded {{ $selectedAssetId == $asset->id ? 'bg-primary text-white' : 'hover-bg-light' }} cursor-pointer"
                                                    wire:click="toggleAsset({{ $asset->id }})">
                                                    <i
                                                        class="ti {{ in_array($asset->id, $expandedAssets) ? 'ti-chevron-down' : 'ti-chevron-right' }} me-1"></i>
                                                    <i class="ti ti-device me-1"></i>{{ $asset->name }}
                                                    @if (isset($assetSelectionCount[$asset->id]) && $assetSelectionCount[$asset->id] > 0)
                                                        <span
                                                            class="badge {{ $selectedAssetId == $asset->id ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill ms-auto">{{ $assetSelectionCount[$asset->id] }}</span>
                                                    @endif
                                                </div>

                                                <!-- Parameters within this asset -->
                                                @if (in_array($asset->id, $expandedAssets) && $selectedAssetId == $asset->id)
                                                    <div class="ms-4 mt-1 parameter-list">
                                                        @if (count($parameters) > 0)
                                                            {{-- urutkan parameter berdasarkan nama --}}
                                                            @php
                                                                $parameters = $parameters->sortBy('name');
                                                            @endphp
                                                            {{-- Loop through parameters --}}
                                                            @foreach ($parameters as $param)
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        id="param-{{ $param['id'] }}"
                                                                        value="{{ $param['id'] }}"
                                                                        wire:click="toggleParameter({{ $param['id'] }})"
                                                                        wire:key="param-{{ $param['id'] }}"
                                                                        {{ in_array($param['id'], $selectedParameters) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="param-{{ $param['id'] }}">
                                                                        {{ $param['name'] }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-muted small ps-2 py-1">
                                                                No parameters available</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @empty
                                                <div class="text-muted small ps-2 py-1">No assets in
                                                    this group
                                                </div>
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
