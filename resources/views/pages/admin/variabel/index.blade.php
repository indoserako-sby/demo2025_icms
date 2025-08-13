@extends('layouts.master')
@push('title')
    Variabel
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
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Data Variabel</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalTambahVariabel">
                                <span class="ti ti-plus me-1"></span> Add Variabel
                            </button>


                        </div>
                        <table class="datatables-basic table table-variable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Variabel Name</th>
                                    <th>Unit</th>
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
                            <h5 class="card-title mb-0">Data Actual</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalTambahActual">
                                <span class="ti ti-plus me-1"></span> Add Actual
                            </button>


                        </div>
                        <table class="datatables-basic table table-actual">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Actual</th>
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
    <!-- Modal Tambah Variabel -->
    <div class="modal fade" id="modalTambahVariabel" tabindex="-1" aria-labelledby="modalTambahVariabelLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('datvar.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahVariabelLabel">Add Data Variabel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Variabel Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit</label>
                        <input type="text" class="form-control" id="unit" name="unit"
                            placeholder="e.g. kg, L, °C">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Tambah Actual -->
    <div class="modal fade" id="modalTambahActual" tabindex="-1" aria-labelledby="modalTambahActualLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('datactual.store') }}" method="POST" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahActualLabel">Add Data Actual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Actual</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Variabel -->
    <div class="modal fade" id="modalEditVariabel" tabindex="-1" aria-labelledby="modalEditVariabelLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditVariabelLabel">Edit Data Variabel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name Variabel</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_unit" class="form-label">Unit</label>
                        <input type="text" class="form-control" id="edit_unit" name="unit"
                            placeholder="e.g. kg, L, °C">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Actual -->
    <div class="modal fade" id="modalEditActual" tabindex="-1" aria-labelledby="modalEditActualLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editFormActual" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditActualLabel">Edit Actual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name Actual</label>
                        <input type="text" class="form-control" id="edit_nameActual" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete Variabel -->
    <div class="modal fade" id="modalDeleteVariabel" tabindex="-1" aria-labelledby="modalDeleteVariabelLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeleteVariabelLabel">Delete Variabel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this variabel?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Delete Actual -->
    <div class="modal fade" id="modalDeleteActual" tabindex="-1" aria-labelledby="modalDeleteActualLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteFormActual" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeleteActualLabel">Delete Data Actual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this Actual data?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
        let variabelTable;
        $(document).ready(function() {
            variabelTable = $('.table-variable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('datvar.data') }}',
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'unit',
                    name: 'unit'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
            });

            // Handle Create Form Submit
            $('#modalTambahVariabel form').off('submit').on('submit', function(e) {
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
                        $('#modalTambahVariabel').modal('hide');
                        form[0].reset();
                        variabelTable.ajax.reload();
                        // Show success message
                        toastr.success('Variabel created successfully');
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while creating the variabel');
                        }
                    }
                });
            });

            // Handle Edit Form Submit
            $('#editForm').on('submit', function(e) {
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
                        $('#modalEditVariabel').modal('hide');
                        form[0].reset();
                        variabelTable.ajax.reload();
                        toastr.success('Variabel updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while updating the variabel');
                        }
                    }
                });
            });

            // Handle Delete Form Submit
            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeleteVariabel').modal('hide');
                        variabelTable.ajax.reload();
                        toastr.success('Variabel deleted successfully');
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while deleting the variabel');
                    }
                });
            });
        });
        let actualTable;
        $(document).ready(function() {
            actualTable = $('.table-actual').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('datactual.data') }}',
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
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

            // No need to re-register the variabel form submit handler here!

            // Handle Edit Form Submit
            $('#editFormActual').on('submit', function(e) {
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
                        $('#modalEditActual').modal('hide');
                        form[0].reset();
                        actualTable.ajax.reload();
                        toastr.success('Actual updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while updating the actual');
                        }
                    }
                });
            });

            // Handle Delete Form Submit
            $('#deleteFormActual').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeleteActual').modal('hide');
                        actualTable.ajax.reload();
                        toastr.success('Actual deleted successfully');
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while deleting the actual');
                    }
                });
            });
        });

        function showEditModal(id) {
            $.get(`/admin/datvar/${id}/edit`, function(data) {
                $('#editForm').attr('action', `/admin/datvar/${id}`);
                $('#edit_name').val(data.name);
                $('#edit_unit').val(data.unit);
                $('#modalEditVariabel').modal('show');
            });
        }

        function showEditModalActual(id) {
            $.get(`/admin/datactual/${id}/edit`, function(data) {
                $('#editFormActual').attr('action', `/admin/datactual/${id}`);
                $('#edit_nameActual').val(data.name);
                $('#modalEditActual').modal('show');
            });
        }

        function showDeleteModal(id) {
            $('#deleteForm').attr('action', `/admin/datvar/${id}`);
            $('#modalDeleteVariabel').modal('show');
        }

        function showDeleteModalActual(id) {
            $('#deleteFormActual').attr('action', `/admin/datactual/${id}`);
            $('#modalDeleteActual').modal('show');
        }
    </script>
@endpush
