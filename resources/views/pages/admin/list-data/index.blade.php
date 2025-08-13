@extends('layouts.master')
@push('title')
    List Data Asset
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">List Data</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalTambahListData">
                        <span class="ti ti-plus me-1"></span> Add List Data
                    </button>

                    <!-- Modal Tambah List Data -->
                    <div class="modal fade" id="modalTambahListData" tabindex="-1"
                        aria-labelledby="modalTambahListDataLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formTambahListData" action="{{ route('list-data.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTambahListDataLabel">Add List Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="asset_id" class="form-label">Asset</label>
                                            <select class="form-select select2" id="asset_id" name="asset_id" required>
                                                <option value="">Select Asset</option>
                                                @foreach ($assets as $asset)
                                                    <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- //machine parameters  --}}
                                        <div class="mb-3">
                                            <label for="machine_parameter_id" class="form-label">Machine Parameter</label>
                                            <select class="form-select select2" id="machine_parameter_id"
                                                name="machine_parameter_id" required>
                                                <option value="">Select Machine Parameter</option>
                                                @foreach ($machineParameters as $machineParameter)
                                                    <option value="{{ $machineParameter->id }}">
                                                        {{ $machineParameter->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- poitions --}}
                                        <div class="mb-3">
                                            <label for="position_id" class="form-label">Position</label>
                                            <select class="form-select select2" id="position_id" name="position_id"
                                                required>
                                                <option value="">Select Position</option>
                                                @foreach ($positions as $position)
                                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- datvars --}}
                                        <div class="mb-3">
                                            <label for="datvar_id" class="form-label">Variable</label>
                                            <select class="form-select select2" id="datvar_id" name="datvar_id" required>
                                                <option value="">Select Variable</option>
                                                @foreach ($datvars as $datvar)
                                                    <option value="{{ $datvar->id }}">{{ $datvar->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- warning limit and danger limit  --}}
                                        <div class="mb-3">
                                            <label for="warning_limit" class="form-label">Warning Limit</label>
                                            <input type="number" class="form-control" id="warning_limit"
                                                name="warning_limit" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="danger_limit" class="form-label">Danger Limit</label>
                                            <input type="number" class="form-control" id="danger_limit" name="danger_limit"
                                                required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Edit Asset -->
                    <div class="modal fade" id="modalEditListData" tabindex="-1"
                        aria-labelledby="modalEditListDataLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formEditListData" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditListDataLabel">Edit List Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="edit_asset_id" class="form-label">Asset</label>
                                            <select class="form-select select2" id="edit_asset_id" name="asset_id"
                                                required>
                                                <option value="">Select Asset</option>
                                                @foreach ($assets as $asset)
                                                    <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_machine_parameter_id" class="form-label">Machine
                                                Parameter</label>
                                            <select class="form-select select2" id="edit_machine_parameter_id"
                                                name="machine_parameter_id" required>
                                                <option value="">Select Machine Parameter</option>
                                                @foreach ($machineParameters as $machineParameter)
                                                    <option value="{{ $machineParameter->id }}">
                                                        {{ $machineParameter->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_position_id" class="form-label">Position</label>
                                            <select class="form-select select2" id="edit_position_id" name="position_id"
                                                required>
                                                <option value="">Select Position</option>
                                                @foreach ($positions as $position)
                                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_datvar_id" class="form-label">Variable</label>
                                            <select class="form-select select2" id="edit_datvar_id" name="datvar_id"
                                                required>
                                                <option value="">Select Variable</option>
                                                @foreach ($datvars as $datvar)
                                                    <option value="{{ $datvar->id }}">{{ $datvar->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_warning_limit" class="form-label">Warning Limit</label>
                                            <input type="number" class="form-control" id="edit_warning_limit"
                                                name="warning_limit" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_danger_limit" class="form-label">Danger Limit</label>
                                            <input type="number" class="form-control" id="edit_danger_limit"
                                                name="danger_limit" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Delete List Data -->
                    <div class="modal fade" id="modalDeleteListData" tabindex="-1"
                        aria-labelledby="modalDeleteListDataLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formDeleteListData" method="POST"> <!-- Updated form ID -->
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDeleteListDataLabel">Delete List Data</h5>
                                        <!-- Updated title -->
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this data?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Area</th>
                            <th>Group</th>
                            <th>Asset</th>
                            <th>Parameter</th>
                            <th>Position</th>
                            <th>Variable</th>
                            <th>Warning Limit</th>
                            <th>Danger Limit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    <script>
        let listDataTable;
        $(document).ready(function() {
            $("#asset_id").select2({
                dropdownParent: $('#modalTambahListData'),
                placeholder: "Select Group",
                allowClear: true
            });
            $("#machine_parameter_id").select2({
                dropdownParent: $('#modalTambahListData'),
                placeholder: "Select Parameter",
                allowClear: true
            });
            $("#position_id").select2({
                dropdownParent: $('#modalTambahListData'),
                placeholder: "Select Position",
                allowClear: true
            });
            $("#datvar_id").select2({
                dropdownParent: $('#modalTambahListData'),
                placeholder: "Select Variable",
                allowClear: true
            });


            $("#edit_asset_id").select2({
                dropdownParent: $(
                    '#modalEditListData'), // Fixed: Changed from modalEditAsset to modalEditListData
                placeholder: "Select Group",
                allowClear: true
            });
            $("#edit_machine_parameter_id").select2({
                dropdownParent: $('#modalEditListData'),
                placeholder: "Select Parameter",
                allowClear: true
            });
            $("#edit_position_id").select2({
                dropdownParent: $('#modalEditListData'),
                placeholder: "Select Position",
                allowClear: true
            });
            $("#edit_datvar_id").select2({
                dropdownParent: $('#modalEditListData'),
                placeholder: "Select Variable",
                allowClear: true
            });



            listDataTable = $('.table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [3, 'asc']
                ],
                autoWidth: false,
                ajax: {
                    url: '{{ route('list-data.data') }}',
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    name: 'id',
                    data: 'id'
                }, {
                    data: 'area',
                    name: 'area'
                }, {
                    data: 'group',
                    name: 'group'
                }, {
                    data: 'asset',
                    name: 'asset'
                }, {
                    data: 'machine_parameter',
                    name: 'machine_parameter'
                }, {
                    data: 'position',
                    name: 'position'
                }, {
                    data: 'datvar',
                    name: 'datvar'
                }, {
                    data: 'warning_limit',
                    name: 'warning_limit'
                }, {
                    data: 'danger_limit',
                    name: 'danger_limit'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
            });

            $('#formTambahListData').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modalTambahListData').modal('hide');
                        form[0].reset();
                        $('#asset_id').val('').trigger('change');
                        $('#machine_parameter_id').val('').trigger('change');
                        $('#position_id').val('').trigger('change');
                        $('#datvar_id').val('').trigger('change');
                        $('#warning_limit').val('');
                        $('#danger_limit').val('');

                        listDataTable.ajax.reload();
                        toastr.success('Asset created successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while creating the asset');
                        }
                    }
                });
            });

            $('#formEditListData').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(this);
                formData.append('_method', 'PUT'); // For PUT request
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modalEditListData').modal(
                            'hide'); // Fixed: Changed from modalEditAsset to modalEditListData
                        form[0].reset();
                        $('#edit_asset_id').val('').trigger('change');
                        $('#edit_machine_parameter_id').val('').trigger('change');
                        $('#edit_position_id').val('').trigger('change');
                        $('#edit_datvar_id').val('').trigger('change');
                        $('#edit_warning_limit').val('');
                        $('#edit_danger_limit').val('');
                        listDataTable.ajax.reload();
                        toastr.success(
                            'List data updated successfully'
                        ); // Fixed: Changed message from "Asset" to "List data"
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                'An error occurred while updating the list data'
                            ); // Fixed: Changed message from "asset" to "list data"
                        }
                    }
                });
            });

            $('#formDeleteListData').on('submit', function(
                e) { // Fixed: Changed from formDeleteAsset to formDeleteListData
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeleteListData').modal(
                            'hide'
                        ); // Fixed: Changed from modalDeleteAsset to modalDeleteListData
                        listDataTable.ajax.reload();
                        toastr.success(
                            'List data deleted successfully'
                        ); // Fixed: Changed message from "Asset" to "List data"
                    },
                    error: function(xhr) {
                        toastr.error(
                            'An error occurred while deleting the list data'
                        ); // Fixed: Changed message from "asset" to "list data"
                    }
                });
            });




        });

        function showEditModal(id) {
            $.get(`/admin/list-data/${id}/edit`, function(data) { // Updated URL to match asset
                $('#formEditListData').attr('action', `/admin/list-data/${id}`);
                $('#edit_asset_id').val(data.asset_id).trigger('change');
                $('#edit_machine_parameter_id').val(data.machine_parameter_id).trigger('change');
                $('#edit_position_id').val(data.position_id).trigger('change'); // Added position_id
                $('#edit_datvar_id').val(data.datvar_id).trigger('change'); // Added datvar_id
                $('#edit_warning_limit').val(data.warning_limit);
                $('#edit_danger_limit').val(data.danger_limit);

                $('#modalEditListData').modal('show'); // Updated modal ID
            });
        }

        function showDeleteModal(id) {
            $('#formDeleteListData').attr('action', `list-data/${id}`); // Fixed form action to formDeleteListData
            $('#modalDeleteListData').modal('show'); // Updated modal ID
        }
    </script>
@endpush
