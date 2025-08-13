@extends('dashboard')
@push('title')
    Asset Information
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <style>
        .sidebar-collapsed {
            overflow: hidden;
        }

        .sidebar-collapsed .card {
            display: none;
        }

        .sidebar-collapsed-toggle {
            position: fixed;
            top: 90px !important;
            left: 0;
            z-index: 100;
            height: auto;
            display: flex;
            justify-content: flex-start;
            padding: 15px 0;
            margin-left: 0;
        }

        .sidebar-collapsed-toggle .btn {
            margin-left: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            width: 42px;
            height: 42px;
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
    <div class="row">
        <!-- Left Column - Asset Selection -->
        <div id="asset-sidebar" class="col-md-3 transition-width"
            style="position: sticky; top: 90px !important; z-index: 10;">
            <!-- Toggle button for collapsed state -->
            <div class="sidebar-collapsed-toggle d-none">
                <button id="expand-sidebar" class="btn btn-primary" title="Show Asset Panel">
                    <i class="ti ti-chevron-right"></i>
                </button>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Asset List</h5>
                    <button id="collapse-sidebar" class="btn btn-primary" title="Hide Asset Panel">
                        <i class="ti ti-chevron-left"></i>
                    </button>
                </div>
                <div class="card-body">
                    @livewire('asset-analysis-tree')
                </div>
            </div>
        </div>

        <!-- Right Column - Content Display -->
        <div id="analysis-content" class="col-md-9 transition-width">

            <!-- Pills Navigation -->
            <ul class="nav nav-pills" id="assetTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="pill" data-bs-target="#info-tab-pane"
                        type="button" role="tab" aria-controls="info-tab-pane" aria-selected="true">
                        <i class="ti ti-info-circle me-1"></i>
                        Asset Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="configuration-tab" data-bs-toggle="pill"
                        data-bs-target="#configuration-tab-pane" type="button" role="tab"
                        aria-controls="configuration-tab-pane" aria-selected="false">
                        <i class="ti ti-settings me-1"></i>
                        Configuration
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative" id="alarm-tab" data-bs-toggle="pill"
                        data-bs-target="#alarm-tab-pane" type="button" role="tab" aria-controls="alarm-tab-pane"
                        aria-selected="false">
                        <i class="ti ti-bell me-1"></i>
                        Alarm
                        @livewire('alarm-count')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="pill" data-bs-target="#history-tab-pane"
                        type="button" role="tab" aria-controls="history-tab-pane" aria-selected="false">
                        <i class="ti ti-history me-1"></i>
                        Historical Alarm
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="assetTabContent">
                <!-- Asset Information Tab -->
                <div class="tab-pane  show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab">
                    @livewire('asset-information')
                </div>

                <!-- Configuration Tab -->
                <div class="tab-pane fade" id="configuration-tab-pane" role="tabpanel" aria-labelledby="configuration-tab">
                    @livewire('asset-configuration')
                </div>

                <!-- Current Alarm Tab -->
                <div class="tab-pane fade" id="alarm-tab-pane" role="tabpanel" aria-labelledby="alarm-tab">
                    <div class="">
                        @livewire('current-alarm-table')
                    </div>
                </div>

                <!-- Historical Alarm Tab -->
                <div class="tab-pane fade" id="history-tab-pane" role="tabpanel" aria-labelledby="history-tab">
                    @livewire('historical-alarm-table')
                </div>
            </div>

        </div>
    </div>
    @livewire('parameter-limit-modal')
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar functionality
            const assetSidebar = document.getElementById('asset-sidebar');
            const analysisContent = document.getElementById('analysis-content');
            const collapseSidebarBtn = document.getElementById('collapse-sidebar');
            const expandSidebarBtn = document.getElementById('expand-sidebar');
            const collapsedToggle = assetSidebar.querySelector('.sidebar-collapsed-toggle');

            // Collapse sidebar function
            collapseSidebarBtn.addEventListener('click', function() {
                // Collapse sidebar
                assetSidebar.classList.add('sidebar-collapsed');
                assetSidebar.classList.remove('col-md-3');
                assetSidebar.classList.add('col-md-1');
                collapsedToggle.classList.remove('d-none');

                // Expand content
                analysisContent.classList.remove('col-md-9');
                analysisContent.classList.add('col-md-11');
            });

            // Expand sidebar function
            expandSidebarBtn.addEventListener('click', function() {
                // Expand sidebar
                assetSidebar.classList.remove('sidebar-collapsed');
                assetSidebar.classList.remove('col-md-1');
                assetSidebar.classList.add('col-md-3');
                collapsedToggle.classList.add('d-none');

                // Reduce content width
                analysisContent.classList.remove('col-md-11');
                analysisContent.classList.add('col-md-9');
            });
        });
    </script>
@endpush
