@extends('dashboard')
@push('title')
    Asset Analysis
@endpush
@push('style')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <style>
        /* ApexCharts Tooltip Styling - Transparent Glass Effect */
        .apexcharts-tooltip {
            background: none !important;

            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
            color: #000 !important;
        }

        .apexcharts-tooltip-title {
            background: none !important;

            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 8px 8px 0 0 !important;
            color: #000 !important;
            font-weight: 600 !important;
            padding: 8px 12px !important;
        }

        /* Styling untuk konten tooltip */
        .apexcharts-tooltip-y-group {
            background: transparent !important;
        }

        .apexcharts-tooltip-text-y-label,
        .apexcharts-tooltip-text-y-value {
            background: transparent !important;
            color: #000 !important;
        }

        .apexcharts-tooltip-series-group {
            background: transparent !important;
        }

        /* Force tooltip to always appear above the data point */
        .apexcharts-tooltip {
            transform: translateY(-100%) !important;
        }

        .apexcharts-tooltip.apexcharts-theme-light {
            transform: translateY(-100%) !important;
        }


        .transition-width {
            transition: width 0.3s ease;
        }
    </style>
    <style>
        /* Asset Panel Customizer Style */
        .asset-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: #fff;
            border-left: 1px solid #e0e0e0;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1100;
            transition: right 0.3s ease;
            overflow-y: auto;
        }

        .asset-panel.active {
            right: 0;
        }

        .asset-panel-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .asset-panel-body {
            padding: 1.5rem;
        }

        .panel-toggle-btn {
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            z-index: 1060;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
            background: #696cff;
            color: white;
            transition: all 0.3s ease;
        }

        .panel-toggle-btn:hover {
            background: #5f61e6;
            transform: translateY(-50%) scale(1.1);
        }

        .panel-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .panel-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .asset-panel {
                width: 100%;
                right: -100%;
            }

            .panel-toggle-btn {
                right: 15px;
                width: 45px;
                height: 45px;
            }
        }

        .transition-width {
            transition: width 0.3s ease;
        }

        .hover-bg-light:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Custom styling for filled pills */
        .nav-pills {
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .nav-pills .nav-link {
            border-radius: 50rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            color: #6c757d;
            transition: all 0.2s ease-in-out;
        }

        .nav-pills .nav-link:hover {
            background: rgba(105, 108, 255, 0.08);
            color: #696cff;
        }

        .nav-pills .nav-link.active {
            background: #696cff;
            color: #fff;
        }

        /* Badge center styling */
        .badge-center {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .h-px-20 {
            height: 20px !important;
        }

        .w-px-20 {
            width: 20px !important;
        }

        .ms-1_5 {
            margin-left: 0.375rem !important;
        }

        /* Remove background and padding from tab content */
        .tab-content,
        .tab-content>.tab-pane {
            padding: 0 !important;
            margin: 0 !important;
            background: none !important;
            border: none !important;
        }

        /* Remove any default card styles from tab panes */
        .tab-pane .card {
            box-shadow: none !important;
            border: none !important;
            /* background: transparent !important; */
        }
    </style>
@endpush
@section('content')
    {{-- panel togle  --}}
    <button class="panel-toggle-btn" id="panel-toggle" title="Asset Panel">
        <i class="ti ti-device-desktop"></i>
    </button>

    <!-- Panel Backdrop -->
    <div class="panel-backdrop" id="panel-backdrop"></div>

    <!-- Asset Panel (Customizer Style) -->
    <div class="asset-panel" id="asset-panel">
        <div class="asset-panel-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Asset Selection</h5>
                <button class="btn btn-sm btn-icon btn-outline-secondary" id="panel-close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>
        <div class="asset-panel-body">
            @livewire('asset-analysis-tree')
        </div>
    </div>
    <div class="row">
        <!-- Hidden Form for Component Sync -->
        <input type="hidden" id="selected-parameters-sync" value="">
        <input type="hidden" id="current-asset-id-sync" name="current_asset_id" value="74">
        <input type="hidden" id="chart-update-trigger" value="0">

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif





        <!-- Right Column - Analysis Display -->
        <div id="analysis-content" class="col-lg-12 transition-width">
            <!-- Chart Display -->
            <div class="card mb-3">

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title mb-0">Asset Performance Chart</h5>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <select id="interval-select" class="form-select">
                                    <option value="3-minutes">3 Minutes</option>
                                    <option value="10-minutes">10 Minutes</option>
                                    <option value="15-minutes">15 Minutes</option>
                                    <option value="30-minutes">30 Minutes</option>
                                    <option value="hour">1 Hour</option>
                                    <option value="4-hours">4 Hours</option>
                                    <option value="6-hours">6 Hours</option>
                                    <option value="12-hours">12 Hours</option>
                                    <option value="day">1 Day</option>
                                    <option value="raw" selected>Raw</option>
                                </select>
                            </div>

                            <input type="text" class="form-control bs-daterangepicker-range me-3" id="date-range"
                                placeholder="Select date range" style="width: 220px;">
                            <button id="export-log-data" class="btn btn-primary " title="Export Log Data">
                                <i class="ti ti-file-export me-1"></i> Export Log Data
                            </button>
                        </div>
                    </div>
                    <div id="interval-indicator" class="mb-3 d-none">
                        <div class="alert alert-info py-1 mb-0">
                            <small><i class="ti ti-info-circle me-1"></i> Data ditampilkan dengan interval: <span
                                    id="current-interval-text">Raw</span></small>
                        </div>
                    </div>
                    <div id="asset-chart" style="min-height: 700px;">
                        @livewire('asset-analysis-chart')
                    </div>
                </div>
            </div>

            <!-- Parameters Data Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Parameters</h5>
                </div>
                <div class="card-body">
                    @livewire('asset-analysis-parameters')
                </div>
            </div>
        </div>
    </div>

    <!-- Update Limits Modal -->
    @livewire('parameter-limit-modal')
@endsection

@push('script')
    <!-- Vendors JS -->

    {{-- <script src="https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js"></script> --}}
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>

    <script>
        // Initialize date picker
        document.addEventListener('DOMContentLoaded', function() {
            // Export Log Data Button Event Handler
            const exportButton = document.getElementById('export-log-data');
            if (exportButton) {
                exportButton.addEventListener('click', function() {
                    // Show loading state
                    exportButton.disabled = true;
                    exportButton.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> Exporting...';

                    // Get current asset ID
                    const assetIdInput = document.getElementById('current-asset-id-sync');
                    const assetId = parseInt(assetIdInput.value);

                    // Get selected parameters
                    const paramCheckboxes = document.querySelectorAll('.parameter-checkbox:checked');
                    let paramIds = [];

                    if (paramCheckboxes.length > 0) {
                        paramIds = Array.from(paramCheckboxes).map(cb => parseInt(cb.value));
                    } else {
                        // No parameters selected, show error
                        alert('Please select at least one parameter to export data.');
                        exportButton.disabled = false;
                        exportButton.innerHTML = '<i class="ti ti-file-export me-1"></i> Export Log Data';
                        return;
                    }

                    // Get current date range
                    const dateRangePicker = $('.bs-daterangepicker-range').data('daterangepicker');
                    let startDate, endDate;

                    if (dateRangePicker) {
                        startDate = dateRangePicker.startDate.format('YYYY-MM-DD');
                        endDate = dateRangePicker.endDate.format('YYYY-MM-DD');
                    } else {
                        // Use default date range if picker not available
                        const now = new Date();
                        endDate = now.toISOString().split('T')[0]; // Today
                        startDate = new Date(now.setDate(now.getDate() - 7)).toISOString().split('T')[
                            0]; // Last 7 days
                    }

                    // Create a form to submit the export request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('export-log-data') }}';
                    form.style.display = 'none';

                    // Create CSRF token input
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');
                    form.appendChild(csrfToken);

                    // Create asset_id input
                    const assetIdField = document.createElement('input');
                    assetIdField.type = 'hidden';
                    assetIdField.name = 'asset_id';
                    assetIdField.value = assetId;
                    form.appendChild(assetIdField);

                    // Create parameters input (as multiple inputs for array)
                    paramIds.forEach(function(paramId) {
                        const paramField = document.createElement('input');
                        paramField.type = 'hidden';
                        paramField.name = 'parameters[]';
                        paramField.value = paramId;
                        form.appendChild(paramField);
                    });

                    // Create start_date input
                    const startDateField = document.createElement('input');
                    startDateField.type = 'hidden';
                    startDateField.name = 'start_date';
                    startDateField.value = startDate;
                    form.appendChild(startDateField);

                    // Create end_date input
                    const endDateField = document.createElement('input');
                    endDateField.type = 'hidden';
                    endDateField.name = 'end_date';
                    endDateField.value = endDate;
                    form.appendChild(endDateField);

                    // Include the current interval setting
                    const intervalSelect = document.getElementById('interval-select');
                    const intervalField = document.createElement('input');
                    intervalField.type = 'hidden';
                    intervalField.name = 'interval';
                    intervalField.value = intervalSelect ? intervalSelect.value : 'raw';
                    form.appendChild(intervalField);

                    // Append form to body
                    document.body.appendChild(form);

                    // Add event listener to reset button state after form submission
                    window.addEventListener('focus', function onFocus() {
                        setTimeout(function() {
                            exportButton.disabled = false;
                            exportButton.innerHTML =
                                '<i class="ti ti-file-export me-1"></i> Export Log Data';
                        }, 1000);
                        window.removeEventListener('focus', onFocus);
                    });

                    // Submit the form to trigger file download
                    form.submit();
                });
            }

            // Panel functionality has been moved to a separate event listener block
            // and is now using the asset-panel implementation

            // Set up direct parameter syncing
            function setupDirectParameterSync() {

                // Store these elements for quick access
                const syncInput = document.getElementById('selected-parameters-sync');
                const assetIdInput = document.getElementById('current-asset-id-sync');
                const updateTrigger = document.getElementById('chart-update-trigger');

                // Variable untuk menyimpan tanggal yang dipilih
                // Set default start date to 3 days before today, end date to today
                const today = new Date();
                const endDateObj = today;
                const startDateObj = new Date(today);
                startDateObj.setDate(today.getDate() - 3);

                // Format as YYYY-MM-DD
                let selectedStartDate = startDateObj.toISOString().split('T')[0];
                let selectedEndDate = endDateObj.toISOString().split('T')[0];

                // Listen for parameter checkbox changes directly
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('parameter-checkbox')) {
                        setTimeout(() => {
                            const checkboxes = document.querySelectorAll(
                                '.parameter-checkbox:checked');
                            const paramIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

                            // Update hidden form field
                            syncInput.value = JSON.stringify(paramIds);

                            // Trigger update - change value to force detection
                            updateTrigger.value = Date.now();

                            // Update chart if at least one parameter is selected (regardless of checkbox action)
                            if (paramIds.length > 0) {

                                // Selalu dapatkan nilai tanggal terbaru dari daterangepicker
                                const dateRangePicker = $('.bs-daterangepicker-range').data(
                                    'daterangepicker');

                                // Jika dateRangePicker memiliki nilai, gunakan itu
                                if (dateRangePicker) {
                                    const formattedStartDate = dateRangePicker.startDate.format(
                                        'YYYY-MM-DD');
                                    const formattedEndDate = dateRangePicker.endDate.format(
                                        'YYYY-MM-DD');

                                    // Update global variables untuk sinkronisasi
                                    selectedStartDate = formattedStartDate;
                                    selectedEndDate = formattedEndDate;

                                    updateChartWithParameters(paramIds, parseInt(assetIdInput
                                            .value),
                                        formattedStartDate, formattedEndDate);
                                } else {
                                    // Fallback ke tanggal yang ada
                                    const formattedStartDate = new Date(selectedStartDate)
                                        .toLocaleDateString('sv-SE', {
                                            timeZone: 'Asia/Jakarta'
                                        });
                                    const formattedEndDate = new Date(selectedEndDate)
                                        .toLocaleDateString('sv-SE', {
                                            timeZone: 'Asia/Jakarta'
                                        });

                                    updateChartWithParameters(paramIds, parseInt(assetIdInput
                                            .value),
                                        formattedStartDate, formattedEndDate);
                                }
                            } else {
                                // If no parameters selected, show the "select parameter" message
                                showNoParametersMessage();
                            }
                        }, 200); // Small delay to ensure checkbox state has updated
                    }
                });

                // Listen for select all changes and handle chart updates
                document.addEventListener('change', function(e) {
                    if (e.target && e.target.id === 'selectAllParameters') {

                        // Get the state of the select all checkbox
                        const selectAllChecked = e.target.checked;

                        // Berikan waktu untuk Livewire memperbarui state
                        setTimeout(() => {
                            // Get all parameter checkboxes
                            const allCheckboxes = document.querySelectorAll('.parameter-checkbox');
                            const checkedCheckboxes = document.querySelectorAll(
                                '.parameter-checkbox:checked');

                            // If select all is unchecked, we should have no parameters selected
                            if (!selectAllChecked) {
                                // Manually uncheck all parameter checkboxes
                                allCheckboxes.forEach(cb => {
                                    cb.checked = false;
                                });
                                // Clear the parameters list
                                syncInput.value = JSON.stringify([]);
                                // Show no parameters message
                                showNoParametersMessage();
                                return;
                            }

                            // If select all is checked, proceed with normal parameter update
                            const paramIds = Array.from(checkedCheckboxes).map(cb => parseInt(cb
                                .value));

                            // Update sync input
                            syncInput.value = JSON.stringify(paramIds);

                            // Selalu dapatkan nilai tanggal terbaru dari daterangepicker
                            const dateRangePicker = $('.bs-daterangepicker-range').data(
                                'daterangepicker');

                            // Jika dateRangePicker memiliki nilai, gunakan itu
                            if (dateRangePicker) {
                                const formattedStartDate = dateRangePicker.startDate.format(
                                    'YYYY-MM-DD');
                                const formattedEndDate = dateRangePicker.endDate.format(
                                    'YYYY-MM-DD');

                                // Update global variables untuk sinkronisasi
                                selectedStartDate = formattedStartDate;
                                selectedEndDate = formattedEndDate;

                                // Force chart update with current parameters
                                if (paramIds.length > 0) {
                                    updateChartWithParameters(
                                        paramIds,
                                        parseInt(assetIdInput.value),
                                        formattedStartDate,
                                        formattedEndDate
                                    );
                                } else {
                                    showNoParametersMessage();
                                }
                            } else {
                                // Fallback ke tanggal yang ada jika flatpickr tidak memiliki nilai
                                const formattedStartDate = new Date(selectedStartDate)
                                    .toLocaleDateString('sv-SE', {
                                        timeZone: 'Asia/Jakarta'
                                    });
                                const formattedEndDate = new Date(selectedEndDate)
                                    .toLocaleDateString('sv-SE', {
                                        timeZone: 'Asia/Jakarta'
                                    });

                                // Force chart update with current parameters
                                if (paramIds.length > 0) {
                                    updateChartWithParameters(
                                        paramIds,
                                        parseInt(assetIdInput.value),
                                        formattedStartDate,
                                        formattedEndDate
                                    );
                                } else {
                                    showNoParametersMessage();
                                }
                            }
                        }, 500); // Increased timeout to ensure state is updated
                    }
                });

                // Listen for browser events from Livewire
                window.addEventListener('parameters-updated', function(event) {
                    if (event.detail && event.detail.parameters) {
                        syncInput.value = JSON.stringify(event.detail.parameters);
                        updateTrigger.value = Date.now();
                    }
                });

                // Keep checking for parameters and force update if needed
                setInterval(function() {
                    const checkboxes = document.querySelectorAll('.parameter-checkbox:checked');
                    if (checkboxes.length > 0) {
                        const paramIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

                        // Update if value changed
                        const currentVal = syncInput.value ? JSON.parse(syncInput.value) : [];
                        if (JSON.stringify(currentVal) !== JSON.stringify(paramIds)) {
                            syncInput.value = JSON.stringify(paramIds);

                            // Fire manual update event
                            const event = new CustomEvent('manual-parameters-changed', {
                                detail: {
                                    parameters: paramIds,
                                    assetId: parseInt(assetIdInput.value)
                                }
                            });
                            document.dispatchEvent(event);
                        }

                        // Always update the current asset ID
                        if (!assetIdInput.value) {
                            const wireAssetId = document.querySelector('[wire\\:model*="assetId"]');
                            if (wireAssetId) {
                                assetIdInput.value = wireAssetId.value || '74';
                            }
                        }
                    }
                }, 1000);
            }

            // Call the setup function
            setupDirectParameterSync();

            // Setup date range listener
            document.addEventListener('date-range-changed', function(event) {
                if (event.detail) {
                    const startDate = event.detail.startDate;
                    const endDate = event.detail.endDate;

                    // Get currently selected parameters
                    const syncInput = document.getElementById('selected-parameters-sync');
                    const assetIdInput = document.getElementById('current-asset-id-sync');
                    const paramIds = syncInput.value ? JSON.parse(syncInput.value) : [];
                    const assetId = parseInt(assetIdInput.value) || 74;

                    // Update chart with the new date range
                    if (paramIds.length > 0) {
                        updateChartWithParameters(paramIds, assetId, startDate, endDate);
                    }
                }
            });

            // Trace Livewire events globally
            if (typeof Livewire !== 'undefined') {
                // Only do this in development or debugging

                // Trace all Livewire dispatches
                const originalDispatch = Livewire.dispatch;
                if (originalDispatch) {
                    Livewire.dispatch = function(...args) {
                        return originalDispatch.apply(this, args);
                    }
                }
            }

            if (document.querySelector('.bs-daterangepicker-range')) {
                // Set default date range for testing with your specific dates
                const today = new Date();
                const endDateObj = today;
                const startDateObj = new Date(today);
                startDateObj.setDate(today.getDate() - 3);

                const defaultStartDate = startDateObj.toISOString().split('T')[0];
                const defaultEndDate = endDateObj.toISOString().split('T')[0];

                const bsRangePickerRange = $('.bs-daterangepicker-range');
                bsRangePickerRange.daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    startDate: moment(startDateObj),
                    endDate: moment(endDateObj),
                    opens: isRtl ? 'left' : 'right'
                }, function(start, end, label) {
                    // Format dates in YYYY-MM-DD format
                    const startDate = start.format('YYYY-MM-DD');
                    const endDate = end.format('YYYY-MM-DD');

                    // Dispatch a custom event for our direct parameter sync
                    document.dispatchEvent(new CustomEvent('date-range-changed', {
                        detail: {
                            startDate: startDate,
                            endDate: endDate
                        }
                    }));
                });

                // Adding btn-secondary class in cancel btn
                const bsRangePickerCancelBtn = document.getElementsByClassName('cancelBtn');
                for (var i = 0; i < bsRangePickerCancelBtn.length; i++) {
                    bsRangePickerCancelBtn[i].classList.remove('btn-default');
                    bsRangePickerCancelBtn[i].classList.add('btn-secondary');
                }

                // Setup interval filter select
                const intervalSelect = document.getElementById('interval-select');
                intervalSelect.addEventListener('change', function() {
                    // Get the selected interval value
                    const interval = this.value;

                    // Get current parameters and date range
                    const syncInput = document.getElementById('selected-parameters-sync');
                    const assetIdInput = document.getElementById('current-asset-id-sync');
                    const paramIds = syncInput.value ? JSON.parse(syncInput.value) : [];
                    const assetId = parseInt(assetIdInput.value) || 74;

                    // Get current date range
                    const dateRangePicker = $('.bs-daterangepicker-range').data(
                        'daterangepicker');
                    let startDate, endDate;

                    if (dateRangePicker) {
                        startDate = dateRangePicker.startDate.format('YYYY-MM-DD');
                        endDate = dateRangePicker.endDate.format('YYYY-MM-DD');
                    } else {
                        // Use default date range if picker not available
                        const now = new Date();
                        endDate = now.toISOString().split('T')[0]; // Today
                        startDate = new Date(now.setDate(now.getDate() - 7)).toISOString()
                            .split('T')[0]; // Last 7 days
                    }

                    // Show interval indicator if not raw
                    const intervalIndicator = document.getElementById('interval-indicator');
                    const currentIntervalText = document.getElementById('current-interval-text');

                    if (interval === 'raw') {
                        if (intervalIndicator) {
                            intervalIndicator.classList.add('d-none');
                        }
                    } else {
                        // Set appropriate text based on interval
                        let intervalText = '';
                        switch (interval) {
                            case '3-minutes':
                                intervalText = '3 Minutes';
                                break;
                            case '10-minutes':
                                intervalText = '10 Minutes';
                                break;
                            case '15-minutes':
                                intervalText = '15 Minutes';
                                break;
                            case '30-minutes':
                                intervalText = '30 Minutes';
                                break;
                            case 'hour':
                                intervalText = '1 Hour';
                                break;
                            case '4-hours':
                                intervalText = '4 Hours';
                                break;
                            case '6-hours':
                                intervalText = '6 Hours';
                                break;
                            case '12-hours':
                                intervalText = '12 Hours';
                                break;
                            case 'day':
                                intervalText = '1 Day';
                                break;
                            case 'week':
                                intervalText = '1 Week';
                                break;
                            default:
                                intervalText = interval;
                        }
                        if (currentIntervalText) {
                            currentIntervalText.textContent = intervalText;
                        }
                        if (intervalIndicator) {
                            intervalIndicator.classList.remove('d-none');
                        }
                    }

                    // Update chart with selected interval
                    if (paramIds.length > 0) {
                        updateChartWithParameters(paramIds, assetId, startDate, endDate,
                            interval);
                    }
                });


                // Force trigger date range event on page load

                // Tunggu lebih lama untuk memastikan semua komponen sudah dimuat sempurna
                setTimeout(function() {
                    // Get dates from daterangepicker
                    const dateRangePicker = $('.bs-daterangepicker-range').data('daterangepicker');
                    const startDate = dateRangePicker ? dateRangePicker.startDate.format('YYYY-MM-DD') :
                        defaultStartDate;
                    const endDate = dateRangePicker ? dateRangePicker.endDate.format('YYYY-MM-DD') :
                        defaultEndDate;

                    // Cek dan ambil parameter yang sudah terpilih (jika ada)
                    const paramCheckboxes = document.querySelectorAll('.parameter-checkbox:checked');
                    let paramIds = [];

                    if (paramCheckboxes.length > 0) {
                        paramIds = Array.from(paramCheckboxes).map(cb => parseInt(cb.value));
                    } else {

                        // Jika tidak ada parameter yang dipilih, cari parameter yang tersedia
                        const allParams = document.querySelectorAll('.parameter-checkbox');
                        if (allParams.length > 0) {
                            // Auto-select first parameter
                            const firstParam = allParams[0];
                            firstParam.checked = true;

                            // Update paramIds with the auto-selected parameter
                            paramIds = [parseInt(firstParam.value)];
                        }
                    }

                    // Ambil asset ID
                    const assetIdInput = document.getElementById('current-asset-id-sync');
                    const assetId = parseInt(assetIdInput.value) || 74;

                    if (paramIds.length > 0) {
                        // Update chart langsung dengan API call

                        // Menggunakan manual AJAX request untuk memastikan data diambil
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '/update-chart-parameters', true);
                        xhr.setRequestHeader('Content-Type', 'application/json');
                        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector(
                            'meta[name="csrf-token"]').getAttribute('content'));
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4) {
                                if (xhr.status === 200) {
                                    try {
                                        const response = JSON.parse(xhr.responseText);

                                        if (response.chartData && response.chartData.series && response
                                            .chartData
                                            .series.length > 0) {
                                            renderChartDirect(response.chartData);

                                            // Tambahan: update tampilan parameter
                                            const syncInput = document.getElementById(
                                                'selected-parameters-sync');
                                            syncInput.value = JSON.stringify(paramIds);
                                        } else {
                                            showNoDataMessage();
                                        }
                                    } catch (e) {}
                                } else {}
                            }
                        };
                        xhr.send(JSON.stringify({
                            asset_id: assetId,
                            parameters: paramIds,
                            start_date: startDate,
                            end_date: endDate
                        }));
                    }

                    // Perbarui status input date range
                    selectedStartDate = startDate;
                    selectedEndDate = endDate;

                    // Update form input value
                    const syncInput = document.getElementById('selected-parameters-sync');
                    syncInput.value = JSON.stringify(paramIds);

                }, 2000); // Tunggu 2 detik untuk memastikan DOM dan komponen Livewire sudah dimuat sepenuhnya

                // Add direct event listeners for debugging
                document.addEventListener('livewire:initialized', () => {

                    // Monitor all events on the document
                    const events = ['parametersSelected', 'updateChart', 'assetSelected',
                        'dateRangeChanged'
                    ];
                    events.forEach(eventName => {
                        document.addEventListener(eventName, (e) => {});
                    });

                    // Listen for assetSelected Livewire event to update chart when asset changes
                    Livewire.on('assetSelected', (assetId) => {
                        console.log('Asset changed to:', assetId);

                        // Update the hidden asset ID input
                        const assetIdInput = document.getElementById('current-asset-id-sync');
                        if (assetIdInput) {
                            assetIdInput.value = assetId;
                        }

                        // We need to wait for the parameters component to update
                        // before getting the new parameters for the selected asset
                        setTimeout(() => {
                            // Get any auto-selected parameters after asset change
                            const checkboxes = document.querySelectorAll(
                                '.parameter-checkbox:checked');
                            if (checkboxes.length > 0) {
                                const paramIds = Array.from(checkboxes).map(cb => parseInt(
                                    cb.value));

                                // Update parameters sync input
                                const syncInput = document.getElementById(
                                    'selected-parameters-sync');
                                if (syncInput) {
                                    syncInput.value = JSON.stringify(paramIds);
                                }

                                // Get current date range
                                const dateRangePicker = $('.bs-daterangepicker-range').data(
                                    'daterangepicker');
                                let startDate, endDate;

                                if (dateRangePicker) {
                                    startDate = dateRangePicker.startDate.format(
                                        'YYYY-MM-DD');
                                    endDate = dateRangePicker.endDate.format('YYYY-MM-DD');
                                } else {
                                    // Use default date range
                                    const now = new Date();
                                    endDate = now.toISOString().split('T')[0];
                                    startDate = new Date(now.setDate(now.getDate() - 3))
                                        .toISOString().split('T')[0];
                                }

                                // Update chart with new asset and parameters
                                updateChartWithParameters(paramIds, assetId, startDate,
                                    endDate);
                            } else {
                                showNoParametersMessage();
                            }
                        }, 1000); // Wait 1 second for parameters component to update
                    });
                });
            }

            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);

            // Fungsi untuk menampilkan loading indicator
            function showLoading() {
                const chartElement = document.getElementById('asset-chart');
                if (chartElement) {
                    // Check if loading indicator already exists
                    if (!document.querySelector('#chart-loading')) {
                        const loadingHtml = `
                            <div id="chart-loading" class="position-absolute top-0 left-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 10;">
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div>Loading chart data...</div>
                                </div>
                            </div>
                        `;
                        chartElement.insertAdjacentHTML('beforeend', loadingHtml);
                    }
                }
            }

            // Fungsi untuk menyembunyikan loading indicator
            function hideLoading() {
                const loadingEl = document.querySelector('#chart-loading');
                if (loadingEl) {
                    loadingEl.remove();
                }
            }

            // Fungsi untuk menampilkan pesan error
            function showErrorMessage(message) {
                const chartElement = document.getElementById('asset-chart');
                if (chartElement) {
                    const errorHtml = `
                        <div class="alert alert-danger mt-3" role="alert">
                            ${message}
                        </div>
                    `;
                    chartElement.insertAdjacentHTML('beforeend', errorHtml);

                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        const alert = chartElement.querySelector('.alert');
                        if (alert) alert.remove();
                    }, 5000);
                }
            }

            // Fungsi untuk menampilkan pesan tidak ada parameter terpilih
            function showNoParametersMessage() {
                const chartElement = document.getElementById('asset-chart');
                if (chartElement) {
                    chartElement.innerHTML = `
                        <div class="d-flex justify-content-center align-items-center h-100" style="min-height: 400px;">
                            <div class="text-center text-muted">
                                <i class="ti ti-list-check mb-3" style="font-size: 3rem;"></i>
                                <p>Please select at least one parameter to view the chart</p>
                            </div>
                        </div>
                    `;
                }
            }

            // Fungsi untuk mengirim request update chart ke backend
            function updateChartWithParameters(paramIds, assetId, startDate, endDate, interval = null) {
                // Default ke tanggal hari ini jika tanggal tidak diberikan
                const currentDate = new Date().toISOString().split('T')[0];
                startDate = startDate || currentDate;
                endDate = endDate || currentDate;

                // Get currently selected interval if not provided
                if (interval === null) {
                    const intervalSelect = document.getElementById('interval-select');
                    interval = intervalSelect ? intervalSelect.value : 'raw';
                }

                // Tampilkan loading indicator
                showLoading();

                // Kirim data ke endpoint /update-chart-parameters
                fetch('/update-chart-parameters', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            asset_id: assetId,
                            parameters: paramIds,
                            start_date: startDate, // Selalu sertakan start_date
                            end_date: endDate, // Selalu sertakan end_date
                            interval: interval // Tambahkan interval
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Sembunyikan loading indicator
                        hideLoading();

                        // Langsung render chart dengan data yang diterima dari API
                        if (data.chartData && data.chartData.series && data.chartData.series.length > 0) {
                            // Update interval indicator if provided
                            if (data.interval) {
                                const intervalIndicator = document.getElementById('interval-indicator');
                                const currentIntervalText = document.getElementById('current-interval-text');

                                if (data.interval === 'raw') {
                                    if (intervalIndicator) {
                                        intervalIndicator.classList.add('d-none');
                                    }
                                } else {
                                    // Set appropriate text based on interval
                                    let intervalText = '';
                                    switch (data.interval) {
                                        case '3-minutes':
                                            intervalText = '3 Minutes';
                                            break;
                                        case '10-minutes':
                                            intervalText = '10 Minutes';
                                            break;
                                        case '15-minutes':
                                            intervalText = '15 Minutes';
                                            break;
                                        case '30-minutes':
                                            intervalText = '30 Minutes';
                                            break;
                                        case 'hour':
                                            intervalText = '1 Hour';
                                            break;
                                        case '4-hours':
                                            intervalText = '4 Hours';
                                            break;
                                        case '6-hours':
                                            intervalText = '6 Hours';
                                            break;
                                        case '12-hours':
                                            intervalText = '12 Hours';
                                            break;
                                        case 'day':
                                            intervalText = '1 Day';
                                            break;
                                        default:
                                            intervalText = data.interval;
                                    }
                                    if (currentIntervalText) {
                                        currentIntervalText.textContent = intervalText;
                                    }
                                    if (intervalIndicator) {
                                        intervalIndicator.classList.remove('d-none');
                                    }
                                }
                            }

                            renderChartDirect(data.chartData);
                        } else {
                            showNoDataMessage();
                        }
                    })
                    .catch(error => {
                        // Sembunyikan loading indicator
                        hideLoading();

                        showErrorMessage('Failed to load chart data: ' + error.message);
                    });
            }

            // Fungsi untuk merender chart langsung tanpa melalui event
            function renderChartDirect(chartData) {

                const chartElement = document.getElementById('asset-chart');
                if (!chartElement) {
                    return;
                }

                // Clear existing content in chart element first, including any "no parameters" message
                chartElement.innerHTML = '';

                try {
                    // Pre-process series data for ApexCharts
                    const series = chartData.series.map(s => {
                        return {
                            name: s.name || 'Unknown parameter',
                            data: s.data
                        };
                    });

                    if (series.length === 0) {
                        showNoDataMessage();
                        return;
                    }

                    // Create ApexCharts instance and set options
                    if (window.assetChart) {
                        window.assetChart.destroy();
                    }

                    const options = {
                        series: series,
                        chart: {
                            height: 600,
                            type: 'line',
                            zoom: {
                                enabled: true,
                                type: 'x'
                            },
                            animations: {
                                enabled: true
                            },
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    selection: true,
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                    reset: true
                                }
                            }
                        },
                        title: {
                            text: chartData.title || 'Asset Parameter Analysis',
                            align: 'center'
                        },
                        stroke: {
                            width: 3,
                            curve: 'smooth',
                            lineCap: 'round'
                        },
                        xaxis: {
                            type: 'datetime',
                            labels: {
                                datetimeUTC: false,
                                format: 'yyyy-MM-dd HH:mm'
                            },
                            title: {
                                text: 'Date'
                            }
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            x: {
                                format: 'yyyy-MM-dd HH:mm'
                            },
                            formatter: function(value, {
                                series,
                                seriesIndex
                            }) {
                                let unit = '';
                                if (chartData.yAxis && chartData.yAxis[seriesIndex] && chartData.yAxis[
                                        seriesIndex].unit) {
                                    unit = chartData.yAxis[seriesIndex].unit;
                                }
                                return value.toFixed(2) + (unit ? ' ' + unit : '');
                            }
                        }
                    };

                    // Set up multiple y-axes if not doing cross analysis
                    if (chartData.yAxis && chartData.yAxis.length > 0 && !chartData.crossAnalysis) {
                        options.yaxis = chartData.yAxis.map((axis, index) => {
                            const seriesData = series[index]?.data || [];
                            let values = seriesData.map(point => point[1]).filter(val => !isNaN(val));
                            const warningValue = chartData.yAxis[0].warning;
                            const dangerValue = chartData.yAxis[0].danger;
                            // atur ulang yaxis config min dan maxnya dimana memerhatikan warning dan danger
                            let minValue = Math.min(...values, warningValue || 0, dangerValue || 0);
                            let maxValue = Math.max(...values, warningValue || 0, dangerValue || 0);

                            // Add padding (10% of the range)
                            let range = maxValue - minValue;
                            let padding = range * 0.1;

                            // Calculate dynamic offset for right-side axes based on label length
                            const isRightSide = index % 2 !== 0 && index > 0;

                            const yAxisConfig = {
                                seriesName: series[index]?.name,
                                title: {
                                    text: axis.name + (axis.unit ? ` (${axis.unit})` : ''),
                                    style: {
                                        fontSize: '12px'
                                    }
                                },
                                tickAmount: 5,
                                min: Math.floor(minValue -
                                    padding), // Remove the minValue <= 0 condition
                                max: Math.ceil(maxValue + padding),
                                opposite: isRightSide,
                                showAlways: true,
                                axisBorder: {
                                    show: true
                                },
                                axisTicks: {
                                    show: true
                                },
                                decimalsInFloat: 2,
                                labels: {
                                    formatter: function(value) {
                                        return value.toFixed(2);
                                    }
                                }
                            };

                            // Only add warning and danger lines for the first series if we have exactly one parameter
                            if (index === 0 && chartData.yAxis.length === 1) {
                                const warningValue = chartData.yAxis[0].warning;
                                const dangerValue = chartData.yAxis[0].danger;
                                // atur ulang yaxis config min dan maxnya dimana memerhatikan warning dan danger
                                if (warningValue || dangerValue) {
                                    yAxisConfig.min = Math.min(yAxisConfig.min, warningValue || 0,
                                        dangerValue || 0);
                                    yAxisConfig.max = Math.max(yAxisConfig.max, warningValue || 0,
                                        dangerValue || 0);
                                }



                                if (warningValue || dangerValue) {
                                    options.annotations = {
                                        yaxis: []
                                    };

                                    if (warningValue) {
                                        options.annotations.yaxis.push({
                                            y: warningValue,
                                            borderColor: '#ffc107',
                                            label: {
                                                text: 'Warning' + (warningValue ? ' (' +
                                                    warningValue +
                                                    ')' : ''),
                                                style: {
                                                    color: '#fff',
                                                    background: '#ffc107'
                                                }
                                            }
                                        });
                                    }

                                    if (dangerValue) {
                                        options.annotations.yaxis.push({
                                            y: dangerValue,
                                            borderColor: '#dc3545',
                                            label: {
                                                text: 'Danger' + (dangerValue ? ' (' + dangerValue +
                                                    ')' : ''),
                                                style: {
                                                    color: '#fff',
                                                    background: '#dc3545'
                                                }
                                            }
                                        });
                                    }
                                }
                            }

                            return yAxisConfig;
                        });
                    } else {
                        // Single y-axis configuration with dynamic min/max
                        const allValues = series.reduce((acc, s) => {
                            const values = s.data.map(point => point[1]).filter(val => !isNaN(val));
                            return acc.concat(values);
                        }, []);

                        const minValue = Math.min(...allValues);
                        const maxValue = Math.max(...allValues);
                        const range = maxValue - minValue;
                        const padding = range * 0.1;
                        console.log('Min:', minValue, 'Max:', maxValue, 'Padding:', padding);
                        // Calculate right margin based on number of series and length of labels

                        options.yaxis = {
                            tickAmount: 5,
                            min: Math.floor(minValue - padding), // Remove the minValue <= 0 condition
                            max: Math.ceil(maxValue + padding),
                            axisBorder: {
                                show: true
                            },
                            axisTicks: {
                                show: true
                            },
                            showAlways: true,
                            decimalsInFloat: 2,
                            labels: {
                                formatter: function(value) {
                                    return value.toFixed(2);
                                }
                            }
                        };
                    }

                    window.assetChart = new ApexCharts(chartElement, options);
                    window.assetChart.render();

                } catch (error) {
                    showNoDataMessage();
                }
            }

            // Fungsi untuk menampilkan pesan tidak ada data
            function showNoDataMessage() {
                const chartElement = document.getElementById('asset-chart');
                if (chartElement) {
                    chartElement.innerHTML = `
                        <div class="d-flex justify-content-center align-items-center h-100" style="min-height: 400px;">
                            <div class="text-center text-muted">
                                <i class="ti ti-chart-bar-off mb-3" style="font-size: 3rem;"></i>
                                <p>No data available for the selected parameters and date range</p>
                            </div>
                        </div>
                    `;
                }
            }
        });
    </script>
@endpush

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Panel elements
            const panelToggle = document.getElementById('panel-toggle');
            const panelClose = document.getElementById('panel-close');
            const assetPanel = document.getElementById('asset-panel');
            const panelBackdrop = document.getElementById('panel-backdrop');

            // Open panel function
            function openPanel() {
                assetPanel.classList.add('active');
                panelBackdrop.classList.add('active');
                document.body.style.overflow = 'hidden';

                // Change icon to close
                const icon = panelToggle.querySelector('i');
                icon.className = 'ti ti-x';
                panelToggle.title = 'Close Asset Panel';
            }

            // Close panel function
            function closePanel() {
                assetPanel.classList.remove('active');
                panelBackdrop.classList.remove('active');
                document.body.style.overflow = 'auto';

                // Change icon back to list
                const icon = panelToggle.querySelector('i');
                icon.className = 'ti  ti-device-desktop';
                panelToggle.title = 'Asset Panel';
            }

            // Toggle panel
            panelToggle.addEventListener('click', function() {
                if (assetPanel.classList.contains('active')) {
                    closePanel();
                } else {
                    openPanel();
                }
            });

            // Close button
            panelClose.addEventListener('click', closePanel);

            // Close when clicking backdrop
            panelBackdrop.addEventListener('click', closePanel);

            // Close on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && assetPanel.classList.contains('active')) {
                    closePanel();
                }
            });

            // Swipe gesture for mobile
            let startX = 0;
            let currentX = 0;
            let isSwipeStarted = false;

            assetPanel.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
                isSwipeStarted = true;
            }, {
                passive: true
            });

            assetPanel.addEventListener('touchmove', function(e) {
                if (!isSwipeStarted) return;

                currentX = e.touches[0].clientX;
                const diffX = currentX - startX;

                // If swiping right and moved more than 100px, close panel
                if (diffX > 100) {
                    closePanel();
                    isSwipeStarted = false;
                }
            }, {
                passive: true
            });

            assetPanel.addEventListener('touchend', function() {
                isSwipeStarted = false;
            }, {
                passive: true
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                // Close panel on desktop if window becomes too wide (optional)
                if (window.innerWidth > 1200 && assetPanel.classList.contains('active')) {
                    // Uncomment below line if you want to auto-close on large screens
                    // closePanel();
                }
            });
        });
    </script>
@endpush
