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
    <!-- Panel Toggle Button -->
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

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Pills Navigation -->
                <ul class="nav nav-pills mb-4" id="assetTabs" role="tablist">
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
                <div class="tab-content" id="assetTabContent">
                    <!-- Asset Information Tab -->
                    <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab">
                        @livewire('asset-information')
                    </div>

                    <!-- Configuration Tab -->
                    <div class="tab-pane fade" id="configuration-tab-pane" role="tabpanel"
                        aria-labelledby="configuration-tab">
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
    </div>

    @livewire('parameter-limit-modal')
@endsection

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
