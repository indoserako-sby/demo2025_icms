@extends('layouts.master')
@push('title')
    User Management
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
                    <h5 class="card-title mb-0">User Data</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                        <span class="ti ti-plus me-1"></span> Add User
                    </button>

                    <!-- Modal Tambah User -->
                    <div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formTambahUser" action="{{ route('user-management.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTambahUserLabel">Add User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                required>
                                        </div>
                                        <div class="form-password-toggle mb-3">
                                            <label class="form-label" for="basic-default-password12">Password</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="basic-default-password12"
                                                    placeholder="············" aria-describedby="basic-default-password2"
                                                    name="password" required>
                                                <span id="basic-default-password2"
                                                    class="input-group-text cursor-pointer"><i class="ti ti-eye"></i></span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="form-select select2" id="role" name="role" required>
                                                <option value="">Pilih Role</option>
                                                <option value="admin">Admin</option>
                                                <option value="user">User</option>
                                            </select>
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

                    <!-- Modal Edit User -->
                    <div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formEditUser" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditUserLabel">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="edit_name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="edit_name" name="name"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="edit_email" name="email"
                                                required>
                                        </div>
                                        <div class="form-password-toggle mb-3">
                                            <label class="form-label" for="edit_password">Password</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="edit_password"
                                                    placeholder="············" aria-describedby="basic-default-password2"
                                                    name="password">
                                                <span id="basic-default-password2"
                                                    class="input-group-text cursor-pointer"><i
                                                        class="ti ti-eye"></i></span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_role" class="form-label">Role</label>
                                            <select class="form-select select2" id="edit_role" name="role" required>
                                                <option value="">Pilih Role</option>
                                                <option value="admin">Admin</option>
                                                <option value="user">User</option>
                                            </select>
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

                    <!-- Modal Delete Group -->
                    <div class="modal fade" id="modalDeleteUser" tabindex="-1" aria-labelledby="modalDeleteGroupLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formDeleteUser" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDeleteUserLabel">Delete User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this user?</p>
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
                            <th>User</th>
                            <th>Email</th>
                            <th>Roles</th>
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
        let groupTable;
        $('#role').select2({
            dropdownParent: $('#modalTambahUser'),
            placeholder: "Select Role",
            allowClear: true
        });
        $('#edit_role').select2({
            dropdownParent: $('#modalEditUser'),
            placeholder: "Select Role",
            allowClear: true
        });
        $(document).ready(function() {

            groupTable = $('.table').DataTable({
                responsive: true,
                processing: true,
                stateSave: false,
                serverSide: true,
                autoWidth: false,
                order: [
                    [1, 'asc'],
                ],
                ajax: {
                    url: '{{ route('user-management.data') }}',
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
                    data: 'email',
                    name: 'email'
                }, {
                    data: 'role',
                    name: 'role'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                drawCallback: function(settings) {
                    // This ensures row numbers stay consistent
                    var api = this.api();
                    var startIndex = api.context[0]._iDisplayStart;
                    api.column(0).nodes().each(function(cell, i) {
                        cell.innerHTML = startIndex + i + 1;
                    });
                }
            });

            $('#formEditUser').on('submit', function(e) {
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
                        $('#modalEditUser').modal('hide');
                        form[0].reset();
                        var currentPage = groupTable.page();
                        groupTable.ajax.reload(function() {
                            groupTable.page(currentPage).draw('page');
                        }, false);

                        toastr.success('User updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while updating the user');
                        }
                    }
                });
            });

            $('#formDeleteUser').on('submit', function(e) { // Updated from formDeleteGroup to formDeleteUser
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeleteUser').modal(
                            'hide'); // Updated from modalDeleteGroup to modalDeleteUser
                        groupTable.ajax.reload();
                        toastr.success('User deleted successfully'); // Updated success message
                    },
                    error: function(xhr) {
                        toastr.error(
                            'An error occurred while deleting the user'
                        ); // Updated error message
                    }
                });
            });




        });

        function showEditModal(id) {
            $.get(`/admin/user-management/${id}/edit`, function(data) { // Updated from /admin/group to /admin/user
                $('#formEditUser').attr('action',
                    `/admin/user-management/${id}`); // Updated from formEdit to formEditUser
                $('#edit_role').val(data.role).trigger('change'); // Added to set the role
                $('#edit_name').val(data.name);
                $('#edit_email').val(data.email);
                $('#modalEditUser').modal('show'); // Updated from modalEditGroup to modalEditUser
            });
        }

        function showDeleteModal(id) {
            $('#formDeleteUser').attr('action', `/admin/user-management/${id}`); // Updated from /admin/group to /admin/user
            $('#modalDeleteUser').modal('show'); // Updated from modalDeleteGroup to modalDeleteUser
        }
    </script>
@endpush
