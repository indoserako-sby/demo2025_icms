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
        .sidebar-collapsed {
            overflow: hidden;
        }

        .sidebar-collapsed .card {
            display: none;
        }

        .sidebar-collapsed-toggle {
            position: fixed;
            top: 89px;
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
    <div id="analysis-content">
        @livewire('recent-alert-table')


    </div>
@endsection

@push('script')
    <!-- Required JS for daterangepicker -->
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
@endpush
