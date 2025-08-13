<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Recent Alerts</h5>
                    <div class="card-subtitle">
                        Showing alerts from the last 30 days by default
                    </div>
                </div>
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
            <div class="mb-4 row g-3 align-items-end">
                <div class="col-md-4">
                    <select wire:model.live="area" id="area" class="form-control select2 form-select"
                        placeholder="Select Area">
                        <option value="">Select Area</option>
                        @foreach ($areas as $areaOption)
                            <option value="{{ $areaOption }}">{{ $areaOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select wire:model.live="group" id="group" class="form-control select2 form-select"
                        placeholder="Select Group">
                        <option value="">Select Group</option>
                        @foreach ($groups as $groupOption)
                            <option value="{{ $groupOption }}">{{ $groupOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select wire:model.live="asset" id="asset" class="form-control select2 form-select"
                        placeholder="Select Asset">
                        <option value="">Select Asset</option>
                        @foreach ($assets as $assetOption)
                            <option value="{{ $assetOption }}">{{ $assetOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select wire:model.live="parameter" id="parameter" class="form-control select2 form-select"
                        placeholder="Select Parameter">
                        <option value="">Select Parameter</option>
                        @foreach ($parameters as $parameterOption)
                            <option value="{{ $parameterOption['id'] }}">{{ $parameterOption['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="alertType" id="alertType" class="form-control select2 form-select"
                        placeholder="Select Alert Type">
                        <option value="">Select Alert Type</option>
                        @foreach ($alertTypes as $alertTypeOption)
                            <option value="{{ $alertTypeOption }}">{{ ucfirst($alertTypeOption) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" wire:model.live="dateRange" class="form-control bs-daterangepicker-range"
                        placeholder="Select date range" id="bs-daterangepicker-range" />
                </div>
                <div class="col-md-1">
                    <button wire:click="resetFilters" class="btn btn-secondary w-100">Reset</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Area</th>
                            <th>Group</th>
                            <th>Asset</th>
                            <th>Parameter</th>
                            <th>Alert Type</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alerts as $alert)
                            <tr>
                                <td>{{ $alert->listData->asset->group->area->name ?? 'N/A' }}</td>
                                <td>{{ $alert->listData->asset->group->name ?? 'N/A' }}</td>
                                <td>{{ $alert->listData->asset->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $machineParamName = $alert->listData->machineParameter->name ?? 'N/A';
                                        $positionName = $alert->listData->position->name ?? 'N/A';
                                        $datvarName = $alert->listData->datvar->name ?? 'N/A';

                                        if (
                                            strtoupper($machineParamName) === strtoupper($positionName) &&
                                            strtoupper($machineParamName) === strtoupper($datvarName)
                                        ) {
                                            echo $machineParamName;
                                        } else {
                                            echo implode(
                                                ' - ',
                                                array_filter([
                                                    $machineParamName ?: 'N/A',
                                                    $positionName ?: 'N/A',
                                                    $datvarName ?: 'N/A',
                                                ]),
                                            );
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @if ($alert->alert_type === 'warning')
                                        <span
                                            class="badge bg-warning text-dark">{{ ucfirst($alert->alert_type) }}</span>
                                    @elseif ($alert->alert_type === 'danger')
                                        <span class="badge bg-danger">{{ ucfirst($alert->alert_type) }}</span>
                                    @else
                                        {{ ucfirst($alert->alert_type) }}
                                    @endif
                                </td>
                                <td>{{ $alert->start_time ? $alert->start_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>{{ $alert->end_time ? $alert->end_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>
                                    <button wire:click="openAcknowledgeModal({{ $alert->id }})"
                                        class="btn btn-sm btn-outline-primary" title="Acknowledge">
                                        <i class="ti ti-check"></i> Acknowledge
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No alerts found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $alerts->links() }}
            </div>

        </div>
    </div>

    <!-- Include the Acknowledge Alarm Modal -->
    <livewire:acknowledge-alarm-modal />

    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
        <link rel="stylesheet"
            href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
        <script>
            function initSelect2() {
                $('.form-select').each(function() {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                });

                // Inisialisasi baru dengan dropdownParent dan wrap
                $('.form-select').each(function() {
                    var $this = $(this);
                    // Wrap jika belum di-wrap
                    if (!$this.parent().hasClass('position-relative')) {
                        $this.wrap('<div class="position-relative"></div>');
                    }
                    $this.select2({
                        placeholder: $this.attr('placeholder') || 'Select value',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $this.parent()
                    });

                    // Tambahkan option jika value tidak ada di option
                    var selectedVal = $this.val();
                    if (selectedVal && !$this.find('option[value="' + selectedVal + '"]').length) {
                        $this.append('<option value="' + selectedVal + '" selected>' + selectedVal + '</option>');
                    }
                });

                // Bind event handlers setelah inisialisasi Select2
                $('#area').on('change.select2', function(e) {
                    var data = $(this).val();
                    @this.set('area', data);
                });

                $('#group').on('change.select2', function(e) {
                    var data = $(this).val();
                    @this.set('group', data);
                });

                $('#asset').on('change.select2', function(e) {
                    var data = $(this).val();
                    @this.set('asset', data);
                });

                $('#parameter').on('change.select2', function(e) {
                    var data = $(this).val();
                    @this.set('parameter', data);
                });

                $('#alertType').on('change.select2', function(e) {
                    var data = $(this).val();
                    @this.set('alertType', data);
                });

                // Initialize Bootstrap daterangepicker
                let isRtl = document.querySelector('html').getAttribute('dir') === 'rtl';
                const bsRangePickerRange = $('#bs-daterangepicker-range');

                if (bsRangePickerRange.length) {
                    bsRangePickerRange.daterangepicker({
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                                .endOf('month')
                            ]
                        },
                        startDate: moment(@this.get('startDate')),
                        endDate: moment(@this.get('endDate')),
                        opens: isRtl ? 'left' : 'right'
                    }, function(start, end, label) {
                        const dateRangeStr = start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD');
                        @this.set('dateRange', dateRangeStr);
                    });

                    // Adding btn-secondary class in cancel btn
                    const bsRangePickerCancelBtn = document.getElementsByClassName('cancelBtn');
                    for (var i = 0; i < bsRangePickerCancelBtn.length; i++) {
                        bsRangePickerCancelBtn[i].classList.remove('btn-default');
                        bsRangePickerCancelBtn[i].classList.add('btn-secondary');
                    }
                }
            }

            // Inisialisasi pertama kali halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                initSelect2();
            });

            // Untuk Livewire 3.x
            document.addEventListener('livewire:initialized', function() {
                // Event ketika Livewire telah diinisialisasi
                initSelect2();

                // Untuk Livewire 3.x, gunakan on untuk event kustom
                @this.on('filterChanged', () => {
                    setTimeout(function() {
                        initSelect2();
                    }, 100);
                });
            });

            // // Perbarui setiap kali komponen Livewire selesai me-render ulang
            // document.addEventListener('livewire:navigating', function() {
            //     initSelect2();
            // });

            // Hook tambahan untuk memastikan Select2 terinisialisasi setelah update apapun
            // document.addEventListener('livewire:initialized', function() {
            //     Livewire.hook('element.updated', (el, component) => {
            //         if (el.querySelector('.form-select') || el.classList.contains('form-select')) {
            //             setTimeout(() => initSelect2(), 50);
            //         }
            //     });

            //     // Hook for message processing - penting untuk reinisialisasi Select2
            //     Livewire.hook('message.processed', (message, component) => {
            //         setTimeout(() => initSelect2(), 50);
            //     });
            // });
        </script>
    @endpush
</div>
