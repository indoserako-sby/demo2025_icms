<div>
    <style>
        .no-wrap {
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    @if ($assetId)
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-history me-2"></i>
                        Historical Alarms for {{ $asset->name ?? 'Selected Asset' }}
                    </h5>
                    <button wire:click="exportToExcel" wire:loading.attr="disabled" class="btn btn-sm btn-success">
                        <span wire:loading.class.remove="d-none" wire:target="exportToExcel"
                            class="spinner-border spinner-border-sm d-none me-1"></span>
                        <i wire:loading.class="d-none" wire:target="exportToExcel" class="ti ti-file-export me-1"></i>
                        <span wire:loading.class="d-none" wire:target="exportToExcel">Export to Excel</span>
                        <span wire:loading wire:target="exportToExcel">Generating...</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row g-3 mb-4">
                    <!-- Parameter Filter -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Parameter</label>
                        <select wire:model.live="parameter" class="form-select">
                            <option value="">All Parameters</option>
                            @foreach ($parameters as $param)
                                <option value="{{ $param['id'] }}">{{ $param['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Alert Type Filter -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Alert Type</label>
                        <select wire:model.live="alertType" class="form-select">
                            <option value="">All Types</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                        </select>
                    </div>

                    <!-- Alarm Cause Filter -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Alarm Cause</label>
                        <select wire:model.live="alarmCause" class="form-select">
                            <option value="">All Causes</option>
                            <option value="Fake Alarm">Fake Alarm</option>
                            <option value="Mall Function">Mall Function</option>
                            <option value="Test Alarm">Test Alarm</option>
                            <option value="Error Alarm">Error Alarm</option>
                        </select>
                    </div>

                    <!-- Acknowledge Person Filter -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Acknowledge Person</label>
                        <input type="text" wire:model.live="acknowledgePerson" class="form-control"
                            placeholder="Search person...">
                    </div>

                    <!-- Date Range Filters -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" wire:model.live="startDate" class="form-control"
                            max="{{ $endDate }}">
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="form-label">End Date</label>
                        <input type="date" wire:model.live="endDate" class="form-control" min="{{ $startDate }}">
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered mb-4">
                        <thead>
                            <tr>
                                <th nowrap>Parameter</th>
                                <th nowrap>Alert Type</th>
                                <th>Reason</th>
                                <th nowrap>Alarm Time</th>
                                <th nowrap>Date Acknowledge</th>
                                <th nowrap>Machine Time</th>
                                <th nowrap>Machine Person</th>
                                <th nowrap>Alarm Cause</th>
                                <th nowrap>Note</th>
                                <th nowrap>Acknowledge Person</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alarms as $alarm)
                                <tr>
                                    <td nowrap>{{ $this->getFormattedParameterName($alarm) }}</td>
                                    <td nowrap>
                                        <span
                                            class="badge bg-{{ $alarm->alert_type === 'danger' ? 'danger' : 'warning' }}">
                                            {{ ucfirst($alarm->alert_type) }}
                                        </span>
                                    </td>
                                    <td nowrap>{{ $this->getAlarmReason($alarm) }}</td>
                                    <td nowrap>
                                        {{ ($alarm->start_time ? $alarm->start_time->translatedFormat('d F Y H:i') : 'now') .
                                            ' - ' .
                                            ($alarm->end_time ? $alarm->end_time->translatedFormat('d F Y H:i') : 'now') }}
                                    </td>
                                    <td nowrap>
                                        {{ $alarm->acknowledged_at ? $alarm->acknowledged_at->format('Y-m-d H:i') : 'Not Acknowledged' }}
                                    </td>
                                    <td nowrap>
                                        {{ $alarm->starttimemaintenance ? $alarm->starttimemaintenance->format('Y-m-d H:i') : '-' }}
                                        s.d
                                        {{ $alarm->endtimemaintenance ? $alarm->endtimemaintenance->format('Y-m-d H:i') : '-' }}
                                    </td>
                                    <td nowrap>
                                        {{ $alarm->machine_person ? $alarm->machine_person : 'N/A' }}</td>
                                    <td nowrap>{{ ucfirst($alarm->alarm_cause) }}</td>
                                    <td nowrap>{{ $alarm->notes }}</td>
                                    <td nowrap>{{ $alarm->acknowledgedByUser->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No historical alarms found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $alarms->links() }} </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="ti ti-history mb-2" style="font-size: 2.5rem;"></i>
                    <h6 class="text-muted">Please select an asset to view historical alarms</h6>
                </div>
            </div>
    @endif
</div>
</div>
