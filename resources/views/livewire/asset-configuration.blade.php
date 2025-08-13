<div>
    @if ($assetId)
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search parameters..."
                            wire:model.live="search">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 40%">Parameter</th>
                                <th class="text-center" style="width: 20%">Warning Limit</th>
                                <th class="text-center" style="width: 20%">Danger Limit</th>
                                <th class="text-center" style="width: 20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($filteredParameters as $parameter)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="text-body">
                                                @if (strtoupper($parameter->machineParameter->name ?? '') === strtoupper($parameter->position->name ?? ''))
                                                    {{ $parameter->datvar->name }}
                                                @else
                                                    {{ $parameter->machineParameter->name ?? 'N/A' }} -
                                                    {{ $parameter->position->name ?? 'N/A' }} -
                                                    {{ $parameter->datvar->name ?? 'N/A' }}
                                                @endif
                                            </span>
                                            @if ($parameter->datvar)
                                                <small class="text-muted">({{ $parameter->datvar->unit ?? '' }})</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold">
                                            {{ $parameter->warning_limit ?? '0' }}
                                            <small class="text-muted">{{ $parameter->datvar->unit ?? '' }}</small>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold">
                                            {{ $parameter->danger_limit ?? '0' }}
                                            <small class="text-muted">{{ $parameter->datvar->unit ?? '' }}</small>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm px-2"
                                            wire:click="openEditModal({{ $parameter->id }})"
                                            title="Edit Parameter Limits">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        <div class="text-muted">
                                            @if ($search)
                                                <i class="ti ti-search me-1"></i>
                                                No parameters found matching "{{ $search }}"
                                            @else
                                                <i class="ti ti-alert-circle me-1"></i>
                                                No parameters configured for this asset
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @livewire('parameter-limit-modal')
    @else
        <div class="text-center py-5">
            <i class="ti ti-settings mb-2" style="font-size: 3rem; opacity: 0.5"></i>
            <h6 class="text-muted">Select an asset to configure its parameters</h6>
        </div>
    @endif
</div>
