<div>
    @if ($assetId)
        <div class="card text-left">
            <div class="card-body text-left">
                <div class="row mb-4 align-items-center">
                    <div class="col-12 col-md-8">
                        <h5 class="card-title mb-2 mb-md-0">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Current Alarms for {{ $asset->name ?? 'Selected Asset' }}
                        </h5>
                    </div>
                    <div class="col-12 col-md-4 text-md-end mt-2 mt-md-0">
                        <button wire:click="exportExcel" wire:loading.attr="disabled"
                            class="btn btn-sm btn-success w-100 w-md-auto">
                            <span wire:loading.class.remove="d-none" wire:target="exportExcel"
                                class="spinner-border spinner-border-sm d-none me-1"></span>
                            <i wire:loading.class="d-none" wire:target="exportExcel" class="ti ti-file-export me-1"></i>
                            <span wire:loading.class="d-none" wire:target="exportExcel">Export to Excel</span>
                            <span wire:loading wire:target="exportExcel">Generating...</span>
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="search">Parameter</label>
                        <input wire:model.live="search" type="text" class="form-control"
                            placeholder="Search parameters...">
                    </div>
                    <div class="col-md-3">
                        <label for="alertTypeFilter">Alert Type</label>
                        <select wire:model.live="alertTypeFilter" class="form-select">
                            <option value="">All Alert Types</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="startDate">Start Date</label>
                        <input wire:model.live="startDate" type="date" class="form-control" placeholder="Start Date">
                    </div>
                    <div class="col-md-3">
                        <label for="endDate">End Date</label>
                        <input wire:model.live="endDate" type="date" class="form-control" placeholder="End Date">
                    </div>
                </div>



                <!-- Alarms Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('parameter')" style="cursor: pointer;">
                                    Parameter
                                    @if ($sortField === 'parameter')
                                        <i class="ti ti-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('alert_type')" style="cursor: pointer;">
                                    Alert Type
                                    @if ($sortField === 'alert_type')
                                        <i class="ti ti-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th>Reason</th>
                                <th>Value</th>
                                <th wire:click="sortBy('start_time')" style="cursor: pointer;">
                                    Start Alarm
                                    @if ($sortField === 'start_time')
                                        <i class="ti ti-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('end_time')" style="cursor: pointer;">
                                    End Alarm
                                    @if ($sortField === 'end_time')
                                        <i class="ti ti-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alarms as $alarm)
                                <tr>
                                    <td>{{ $this->getFormattedParameterName($alarm) }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $alarm->alert_type === 'danger' ? 'bg-danger' : 'bg-warning' }}">
                                            {{ ucfirst($alarm->alert_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $this->getAlarmReason($alarm) }}</td>
                                    <td nowrap>{{ number_format($alarm->value, 2) }}
                                        {{ $alarm->listData->datvar->unit ?? '' }}</td>
                                    <td nowrap>
                                        {{ $alarm->start_time ? \Carbon\Carbon::parse($alarm->start_time)->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td nowrap>
                                        {{ $alarm->end_time ? \Carbon\Carbon::parse($alarm->end_time)->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td>
                                        <button wire:click="openAcknowledgeModal({{ $alarm->id }})"
                                            class="btn btn-sm btn-outline-primary" title="Acknowledge">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">No unacknowledged alarms found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $alarms->links() }}
                </div>

                <!-- Include the Acknowledge Modal Component -->
                <livewire:acknowledge-alarm-modal :assetId="$assetId" />
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-4">
                <i class="ti ti-alert-circle-off mb-2" style="font-size: 2.5rem;"></i>
                <h6 class="text-muted">Please select an asset to view alarms</h6>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        window.addEventListener('livewire:load', function() {
            Livewire.on('closeModal', function() {
                document.querySelector('.modal-backdrop')?.remove();
            });
        });
    </script>
@endpush
