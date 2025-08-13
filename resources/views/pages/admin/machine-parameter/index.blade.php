@extends('layouts.master')
@push('title')
    Master Parameter
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
        <div class="row d-flex ">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Parameter Data</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalTambahParameter">
                                <span class="ti ti-plus me-1"></span> Add Parameter
                            </button>

                            <!-- Modal Tambah Parameter -->
                            <div class="modal fade" id="modalTambahParameter" tabindex="-1"
                                aria-labelledby="modalTambahParameterLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formTambahParameter" action="{{ route('machine-parameter.store') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTambahParameterLabel">Add Parameter</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Parameter Name</label>
                                                    <input type="text" class="form-control" id="name" name="name"
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

                            <!-- Modal Edit Parameter   -->
                            <div class="modal fade" id="modalEditParameter" tabindex="-1"
                                aria-labelledby="modalEditParameterLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formEditParameter" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEditParameterLabel">Edit Parameter</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label for="edit_name" class="form-label">Parameter Name</label>
                                                    <input type="text" class="form-control" id="edit_name" name="name"
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

                            <!-- Modal Delete Parameter -->
                            <div class="modal fade" id="modalDeleteParameter" tabindex="-1"
                                aria-labelledby="modalDeleteParameterLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formDeleteParameter" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalDeleteParameterLabel">Delete Parameter
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this parameter?</p>
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
                        <table class="datatables-basic table parameter-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Id</th>
                                    <th>Parameter</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Data Position</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalTambahPosition">
                                <span class="ti ti-plus me-1"></span> Add Position
                            </button>

                            <!-- Modal Tambah Parameter -->
                            <div class="modal fade" id="modalTambahPosition" tabindex="-1"
                                aria-labelledby="modalTambahPositionLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formTambahPosition" action="{{ route('position.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTambahPositionLabel">Add Position
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Position Name</label>
                                                    <input type="text" class="form-control" id="name"
                                                        name="name" required>
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

                            <!-- Modal Edit Position   -->
                            <div class="modal fade" id="modalEditPosition" tabindex="-1"
                                aria-labelledby="modalEditPositionLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formEditPosition" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEditPositionLabel">Edit Position</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label for="edit_name" class="form-label">Position Name</label>
                                                    <input type="text" class="form-control" id="edit_nameposition"
                                                        name="name" required>
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

                            <!-- Modal Delete Position -->
                            <div class="modal fade" id="modalDeletePosition" tabindex="-1"
                                aria-labelledby="modalDeletePositionLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formDeletePosition" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalDeletePositionLabel">Delete Position
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this position?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <table class="datatables-basic table position-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Id</th>
                                    <th>Position</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    <script>
        let parameterTable;

        $(document).ready(function() {

            parameterTable = $('.parameter-table').DataTable({
                responsive: true,
                processing: true,
                stateSave: false,
                serverSide: true,
                autoWidth: false,
                order: [
                    [1, 'asc'],
                ],
                ajax: {
                    url: '{{ route('machine-parameter.data') }}',
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
            });
            positionTable = $('.position-table').DataTable({
                responsive: true,
                processing: true,
                stateSave: false,
                serverSide: true,
                autoWidth: false,
                order: [
                    [1, 'asc'],
                ],
                ajax: {
                    url: '{{ route('position.data') }}',
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
            });


            $('#formEditPosition').on('submit', function(e) {
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
                        $('#modalEditPosition').modal('hide');
                        form[0].reset();
                        positionTable.ajax.reload();

                        toastr.success('Position updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while updating the Position');
                        }
                    }
                });
            });
            $('#formEditParameter').on('submit', function(e) {
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
                        $('#modalEditParameter').modal('hide');
                        form[0].reset();
                        parameterTable.ajax.reload();

                        toastr.success('Parameter updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while updating the parameter');
                        }
                    }
                });
            });

            $('#formDeleteParameter').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeleteParameter').modal('hide'); // Updated modal ID
                        parameterTable.ajax.reload();
                        toastr.success(
                            'Parameter deleted successfully'); // Updated success message
                    },
                    error: function(xhr) {
                        toastr.error(
                            'An error occurred while deleting the parameter'
                        ); // Updated error message
                    }
                });
            });
            $('#formDeletePosition').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeletePosition').modal('hide'); // Updated modal ID
                        positionTable.ajax.reload();
                        toastr.success(
                            'Position deleted successfully'); // Updated success message
                    },
                    error: function(xhr) {
                        toastr.error(
                            'An error occurred while deleting the position' // Updated error message
                        ); // Updated error message
                    }
                });
            });




        });

        function showEditModal(id) {
            $.get(`/admin/machine-parameter/${id}/edit`, function(data) { // Updated URL to match the correct resource
                $('#formEditParameter').attr('action', `/admin/machine-parameter/${id}`); // Use /admin prefix
                $('#edit_name').val(data.name);
                $('#modalEditParameter').modal('show'); // Updated modal ID
            });
        }

        function showEditModalPosition(id) {
            $.get(`/admin/position/${id}/edit`, function(data) { // Updated URL to match the correct resource
                $('#formEditPosition').attr('action', `/admin/position/${id}`); // Use /admin prefix
                $('#edit_nameposition').val(data.name);
                $('#modalEditPosition').modal('show'); // Updated modal ID
            });
        }

        function showDeleteModalPosition(id) {
            $('#formDeletePosition').attr('action',
                `/admin/position/${id}`);
            $('#modalDeletePosition').modal('show'); // Updated modal ID
        }

        function showDeleteModal(id) {
            $('#formDeleteParameter').attr('action',
                `/admin/machine-parameter/${id}`);
            $('#modalDeleteParameter').modal('show'); // Updated modal ID
        }
    </script>
@endpush
