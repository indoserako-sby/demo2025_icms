@extends('dashboard')
@push('title')
    Dashboard
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
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
@endpush
@section('content')
    <div class="row">
        @livewire('area-count')
        @livewire('group-count')
        @livewire('asset-count')
        @livewire('list-data-count')
        <div class="col-md-6 mt-4">
            <div class="row">
                <div class="col-12 ">
                    <h5>Warning Conditions</h5>
                </div>
                @livewire('area-warning-count')
                @livewire('group-warning-count')
                @livewire('asset-warning-count')
                @livewire('list-data-warning-count')
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="row">
                <div class="col-12 ">
                    <h5>Danger Conditions</h5>
                </div>
                @livewire('area-danger-count')
                @livewire('group-danger-count')
                @livewire('asset-danger-count')
                @livewire('list-data-danger-count')

            </div>
        </div>
        <div class="col-md-8">
            @livewire('nested-data-table')
        </div>
        <div class="col-md-4 mt-4">
            @livewire('asset-warning-danger-table')
        </div>

    </div>
@endsection

@push('script')
@endpush
