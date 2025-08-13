<div>
    @if ($asset)
        <!-- Equipment Status Card -->
        <div class="card mb-2">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="ti ti-chart-dots me-2"></i>Equipment Status</h5>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="border rounded p-2">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-check text-success fs-3 me-2"></i>
                                <div>
                                    <div class="text-muted small">Good Condition</div>
                                    <h5 class="mb-0">{{ $asset->listData->where('condition', 'good')->count() }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-2">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-alert-triangle text-warning fs-3 me-2"></i>
                                <div>
                                    <div class="text-muted small">Warning</div>
                                    <h5 class="mb-0">{{ $asset->listData->where('condition', 'warning')->count() }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-2">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-alert-circle text-danger fs-3 me-2"></i>
                                <div>
                                    <div class="text-muted small">Danger</div>
                                    <h5 class="mb-0">{{ $asset->listData->where('condition', 'danger')->count() }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Asset Information Card -->
        <div class="card mb-2">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="ti ti-info-circle me-2"></i>Asset Information</h5>
                <div class="row g-2">
                    <!-- Area and Asset Images -->
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-md-6">
                                @if ($asset->group->area->image)
                                    <img src="{{ Storage::url($asset->group->area->image) }}"
                                        alt="{{ $asset->group->area->name }}" class="img-fluid rounded border"
                                        style="width: 100%; height: 180px; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center  rounded border"
                                        style="height: 180px;">
                                        <i class="ti ti-map-pin-off text-secondary" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <small class="fw-bold"><i
                                            class="ti ti-building-factory me-1"></i>{{ $asset->group->area->name }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if ($asset->image)
                                    <img src="{{ Storage::url($asset->image) }}" alt="{{ $asset->name }}"
                                        class="img-fluid rounded border"
                                        style="width: 100%; height: 180px; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center  rounded border"
                                        style="height: 180px;">
                                        <i class="ti ti-photo-off text-secondary" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <small class="fw-bold"><i class="ti ti-machine me-1"></i>Equipment Image</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Details -->
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="border rounded p-2  h-100">
                                    <label class="fw-bold d-block mb-1 small"><i class="ti ti-id me-1"></i>Asset
                                        Name</label>
                                    <span>{{ $asset->name }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-2  h-100">
                                    <label class="fw-bold d-block mb-1 small"><i
                                            class="ti ti-barcode me-1"></i>Code</label>
                                    <span>{{ $asset->code }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-2  h-100">
                                    <label class="fw-bold d-block mb-1 small"><i class="ti ti-map me-1"></i>Area</label>
                                    <span>{{ $asset->group->area->name }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-2  h-100">
                                    <label class="fw-bold d-block mb-1 small"><i
                                            class="ti ti-boxes me-1"></i>Group</label>
                                    <span>{{ $asset->group->name }}</span>
                                </div>
                            </div>
                            @if ($asset->description)
                                <div class="col-12">
                                    <div class="border rounded p-2 ">
                                        <label class="fw-bold d-block mb-1 small"><i
                                                class="ti ti-file-description me-1"></i>Description</label>
                                        <span>{{ $asset->description }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variable Monitoring Card -->
        <div class="card ">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="ti ti-chart-dots me-2"></i>Variable Monitoring</h5>
                <div class="row g-2">
                    @forelse($asset->listData as $variable)
                        <div class="col-sm-6">
                            <div class="border rounded p-2 h-100">
                                <label class="fw-bold d-block mb-1 small">
                                    <i class="ti ti-gauge me-1"></i>
                                    @if (strtoupper($variable->machineParameter->name ?? '') === strtoupper($variable->position->name ?? ''))
                                        {{ $variable->datvar->name }}
                                    @else
                                        {{ $variable->machineParameter->name ?? 'N/A' }} -
                                        {{ $variable->position->name ?? 'N/A' }} -
                                        {{ $variable->datvar->name ?? 'N/A' }}
                                    @endif
                                </label>
                                <div class="d-flex justify-content-between align-items-center">
                                    @if (!is_null($variable->value))
                                        <span>{{ number_format($variable->value, 2) }}
                                            {{ $variable->datvar->unit ?? '' }}</span>
                                    @else
                                        <span>N/A</span>
                                    @endif
                                    @if ($variable->condition === 'good')
                                        <span class="badge bg-success">Good</span>
                                    @elseif($variable->condition === 'warning')
                                        <span class="badge bg-warning">Warning</span>
                                    @elseif($variable->condition === 'danger')
                                        <span class="badge bg-danger">Danger</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-3 text-muted">
                                No variables found for this asset
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div class="card ">
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="ti ti-device-desktop-off mb-2" style="font-size: 2.5rem;"></i>
                    <h6 class="text-muted">Please Select Equipment to View Details</h6>
                </div>
            </div>
        </div>
    @endif
</div>
