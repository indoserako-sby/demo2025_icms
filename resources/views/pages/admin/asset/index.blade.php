@extends('layouts.master')
@push('title')
    Asset
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
                    <h5 class="card-title mb-0">Data Asset</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalTambahAsset">
                        <span class="ti ti-plus me-1"></span> Add Asset
                    </button>

                    <!-- Modal Tambah Asset -->
                    <div class="modal fade" id="modalTambahAsset" tabindex="-1" aria-labelledby="modalTambahAssetLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formTambahAsset" action="{{ route('asset.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTambahAssetLabel">Add Asset</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="group_id" class="form-label">Group</label>
                                            <select class="form-select select2" id="group_id" name="group_id" required>
                                                <option value="">Select Group</option>
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }} -
                                                        {{ $group->area->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Asset Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="code" class="form-label">Code</label>
                                            <input type="text" class="form-control" id="code" name="code"
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

                    <!-- Modal Edit Asset -->
                    <div class="modal fade" id="modalEditAsset" tabindex="-1" aria-labelledby="modalEditAssetLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formEditAsset" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditAssetLabel">Edit Asset</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="edit_group_id" class="form-label">Group</label>
                                            <select class="form-select select2" id="edit_group_id" name="group_id"
                                                required>
                                                <option value="">Select Group</option>
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }} -
                                                        {{ $group->area->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_name" class="form-label">Asset Name</label>
                                            <input type="text" class="form-control" id="edit_name" name="name"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_code" class="form-label">Code</label>
                                            <input type="text" class="form-control" id="edit_code" name="code"
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

                    <!-- Modal Delete Asset -->
                    <div class="modal fade" id="modalDeleteAsset" tabindex="-1" aria-labelledby="modalDeleteAssetLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="formDeleteAsset" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDeleteAssetLabel">Delete Asset</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this asset?</p>
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
                            <th>Group</th>
                            <th>Code</th>
                            <th>Asset Name</th>
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
        $(document).ready(function() {
            $("#group_id").select2({
                dropdownParent: $('#modalTambahAsset'),
                placeholder: "Select Group",
                allowClear: true
            });
            $("#edit_group_id").select2({
                dropdownParent: $('#modalEditAsset'), // Updated to match the modal ID
                placeholder: "Select Group",
                allowClear: true
            });
            groupTable = $('.table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                autoWidth: false,
                ajax: {
                    url: '{{ route('asset.data') }}',
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
                    data: 'group',
                    name: 'group'
                }, {
                    data: 'code',
                    name: 'code'
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
            });

            $('#formTambahAsset').on('submit', function(e) {
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
                        $('#modalTambahAsset').modal('hide');
                        form[0].reset();
                        $('#group_id').val('').trigger('change');
                        groupTable.ajax.reload();
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

            $('#formEditAsset').on('submit', function(e) {
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
                        $('#modalEditAsset').modal('hide'); // Updated modal ID
                        form[0].reset();
                        $('#edit_group_id').val('').trigger('change');
                        $('#current_image').html('');
                        groupTable.ajax.reload();
                        toastr.success('Asset updated successfully'); // Updated success message
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                'An error occurred while updating the asset'
                            ); // Updated error message
                        }
                    }
                });
            });

            $('#formDeleteAsset').on('submit', function(e) { // Fixed the ID to formDeleteAsset
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#modalDeleteAsset').modal('hide'); // Updated modal ID
                        groupTable.ajax.reload();
                        toastr.success('Asset deleted successfully'); // Updated success message
                    },
                    error: function(xhr) {
                        toastr.error(
                            'An error occurred while deleting the asset'
                        ); // Updated error message
                    }
                });
            });




        });

        function showEditModal(id) {
            $.get(`/admin/asset/${id}/edit`, function(data) { // Updated URL to match asset
                $('#formEditAsset').attr('action', `/admin/asset/${id}`);
                $('#edit_group_id').val(data.group_id).trigger('change');
                $('#edit_code').val(data.code);
                $('#edit_name').val(data.name);
                $('#edit_description').val(data.description);
                if (data.image) {
                    $('#current_image').html(
                        `<img src="/storage/${data.image}" alt="Current Image" width="100">`);
                }
                $('#modalEditAsset').modal('show'); // Updated modal ID
            });
        }

        function showDeleteModal(id) {
            $('#formDeleteAsset').attr('action', `admin/asset/${id}`); // Fixed form action to formDeleteAsset
            $('#modalDeleteAsset').modal('show'); // Updated modal ID
        }
    </script>
@endpush
