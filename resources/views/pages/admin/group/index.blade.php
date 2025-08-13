@extends('layouts.master')
@push('title')
    Group
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
                    <h5 class="card-title mb-0">Group Data</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalTambahGroup">
                        <span class="ti ti-plus me-1"></span> Add Group
                    </button>

                    <!-- Modal Tambah Group -->
                    <div class="modal fade" id="modalTambahGroup" tabindex="-1" aria-labelledby="modalTambahGroupLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formTambahGroup" action="{{ route('group.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTambahGroupLabel">Add Group</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="area_id" class="form-label">Area</label>
                                            <select class="form-select select2" id="area_id" name="area_id" required>
                                                <option value="">Select Area</option>
                                                @foreach ($areas as $area)
                                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Group Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image</label>
                                            <input type="file" class="form-control" id="image" name="image"
                                                accept="image/*">
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

                    <!-- Modal Edit Group -->
                    <div class="modal fade" id="modalEditGroup" tabindex="-1" aria-labelledby="modalEditGroupLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formEditGroup" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditGroupLabel">Edit Group</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="edit_area_id" class="form-label">Area</label>
                                            <select class="form-select select2" id="edit_area_id" name="area_id"
                                                required>
                                                <option value="">Select Area</option>
                                                @foreach ($areas as $area)
                                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_name" class="form-label">Group Name</label>
                                            <input type="text" class="form-control" id="edit_name" name="name"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_description" class="form-label">Description</label>
                                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_image" class="form-label">Image</label>
                                            <input type="file" class="form-control" id="edit_image" name="image"
                                                accept="image/*">
                                            <div id="current_image" class="mt-2"></div>
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
                    <div class="modal fade" id="modalDeleteGroup" tabindex="-1" aria-labelledby="modalDeleteGroupLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formDeleteGroup" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDeleteGroupLabel">Delete Group</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this group?</p>
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
                            <th>Area</th>
                            <th>Group Name</th>
                            <th>Description</th>
                            <th>Image</th>
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
        $('#area_id').select2({
            dropdownParent: $('#modalTambahGroup'),
            placeholder: "Pilih Area",
            allowClear: true
        });
        $('#edit_area_id').select2({
            dropdownParent: $('#modalEditGroup'),
            placeholder: "Pilih Area",
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

                    [2, 'asc']
                ],
                ajax: {
                    url: '{{ route('group.data') }}',
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'area',
                    name: 'area'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'description',
                    name: 'description'
                }, {
                    data: 'image',
                    name: 'image',
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

            $('#formEditGroup').on('submit', function(e) {
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
                        $('#modalEditGroup').modal('hide');
                        form[0].reset();
                        $('#current_image').html('');
                        var currentPage = groupTable.page();
                        groupTable.ajax.reload(function() {
                            groupTable.page(currentPage).draw('page');
                        }, false);

                        toastr.success('Group updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred while updating the group');
                        }
                    }
                });
            });

            $('#formDeleteGroup').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeleteGroup').modal('hide');
                        groupTable.ajax.reload();
                        toastr.success('Group deleted successfully');
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while deleting the group');
                    }
                });
            });




        });

        function showEditModal(id) {
            $.get(`/admin/group/${id}/edit`, function(data) {
                $('#formEditGroup').attr('action', `/admin/group/${id}`); // Use /admin prefix
                $('#edit_area_id').val(data.area_id).trigger('change');
                $('#edit_name').val(data.name);
                $('#edit_description').val(data.description);
                if (data.image) {
                    $('#current_image').html(
                        `<img src="/storage/${data.image}" alt="Current Image" width="100">`);
                } else {
                    $('#current_image').html('');
                }
                $('#modalEditGroup').modal('show');
            });
        }

        function showDeleteModal(id) {
            $('#formDeleteGroup').attr('action', `/admin/group/${id}`); // Use /admin prefix
            $('#modalDeleteGroup').modal('show');
        }
    </script>
@endpush
