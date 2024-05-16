@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-2">Roles List</h4>

        <!-- Role cards -->
        <div class="row g-4">
            <div class="col-12">
                <!-- Role Table -->
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-roles table border-top">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Access Role</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!--/ Role Table -->
            </div>
        </div>
        <!--/ Role cards -->
        @if (!empty(array_intersect(['role-create', 'role-edit'], auth()->user()->list_role)))
            <!-- Add Role Modal -->
            <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
                    <div class="modal-content p-1 p-md-4">
                        <div class="modal-body">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-center mb-4">
                                <h3 class="role-title" id="modal-title">Add New Role</h3>
                                <p>Set role permissions</p>
                            </div>
                            <!-- Add role form -->
                            <form id="addRoleForm" class="row g-3" onsubmit="return false">
                                @csrf
                                <div class="col-12 mb-4">
                                    <label class="form-label" for="name">Role Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder="Enter a role name" />
                                </div>
                                <div class="col-12">
                                    <h4>Role Permissions</h4>
                                    <!-- Permission table -->
                                    <div class="table-responsive">
                                        <table class="table table-flush-spacing">
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap fw-semibold">
                                                        Administrator Access
                                                        <i class="bx bx-info-circle bx-xs" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Allows a full access to the system"></i>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="selectAll" />
                                                            <label class="form-check-label" for="selectAll"> Select All
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @foreach ($roles as $name => $role)
                                                    <tr>
                                                        <td class="text-nowrap fw-semibold">{{ $name }}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                @foreach ($role as $item)
                                                                    <div class="form-check me-3 me-lg-5">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="permission" name="permission[]"
                                                                            value="{{ $item->id }}" />
                                                                        <label class="form-check-label" for="permission">
                                                                            {{ $item->name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Permission table -->
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                            <!--/ Add role form -->
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Add Role Modal -->
        @endif
    </div>
@endsection

@push('scripts')
    {{-- <script src="{{ asset('admin/js/app-access-roles.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin/js/modal-add-role.js') }}"></script> --}}

    <script>
        $(document).ready(function() {
            let isEdit = false;
            var table = $('.datatables-roles').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('roles.list') }}",
                columns: [{
                        data: 'empty'
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'role_category'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action'
                    }
                ],
                columnDefs: [{
                        // For Responsive
                        className: 'control',
                        orderable: false,
                        responsivePriority: 2,
                        targets: 0,
                        render: function(data, type, full, meta) {
                            return '';
                        }
                    }, {
                        targets: 3,
                        render: function(data, type, full, meta) {
                            var roles = data.map((role, i) => {
                                return `<span class="badge bg-primary">${role}</span>`
                            })

                            return roles;
                        }
                    },
                    {
                        // Actions
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            // return '<a href="javascript:;" class="btn btn-sm btn-icon btn-primary detail-record" data-id="' +
                            //     data + '"><i class="bx bx-show"></i></a>' +
                            //     '&nbsp' +
                            //     '<a href="javascript:;" class="btn btn-sm btn-icon btn-secondary btn-edit" data-id="' +
                            //     data + '"><i class="bx bx-edit"></i></a>' +
                            //     '&nbsp' +
                            //     '<a href="javascript:;" class="btn btn-sm btn-icon btn-danger delete-record" data-id="' +
                            //     data + '"><i class="bx bx-trash"></i></a>';

                            let buttons = '';
                            buttons +=
                                '<a href="javascript:;" class="btn btn-sm btn-icon btn-primary detail-record" data-id="' +
                                data + '"><i class="bx bx-show"></i></a>';

                            @if (!empty(array_intersect(['role-edit'], auth()->user()->list_role)))
                                buttons +=
                                    ' <a href="javascript:;" class="btn btn-sm btn-icon btn-secondary btn-edit" data-id="' +
                                    data + '"><i class="bx bx-edit"></i></a> ';
                            @endif

                            @if (!empty(array_intersect(['role-delete'], auth()->user()->list_role)))
                                buttons +=
                                    '<a href="javascript:;" class="btn btn-sm btn-icon btn-danger delete-record" data-id="' +
                                    data + '"><i class="bx bx-trash"></i></a>';
                            @endif

                            return buttons;
                        }
                    }
                ],
                order: [
                    [2, 'desc']
                ],
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 7,
                lengthMenu: [7, 10, 25, 50, 75, 100],
                buttons: [
                    @if (!empty(array_intersect(['role-create'], auth()->user()->list_role)))
                        {
                            text: '<i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
                            className: 'create-new btn btn-primary',
                            action: function(e, dt, node, config) {
                                $('#addRoleModal').modal('show');
                            }
                        }
                    @endif

                ],

            });

            $('div.head-label').html('<h5 class="card-title mb-0">Table Roles</h5>');

            @if (!empty(array_intersect(['role-edit', 'role-create'], auth()->user()->list_role)))
                const selectAll = document.querySelector('#selectAll'),
                    checkboxList = document.querySelectorAll('[type="checkbox"]');

                selectAll.addEventListener('change', t => {
                    checkboxList.forEach(e => {
                        e.checked = t.target.checked;
                    });
                });
            @endif

            $(document).on('submit', '#addRoleForm', function(e) {
                e.preventDefault();

                let url = "{{ route('roles.store') }}";

                if (isEdit) {
                    const id = $('#role-id').val();
                    url = "{{ route('roles.update', ':id') }}";
                    url = url.replace(':id', id);
                }

                var method = isEdit ? "PUT" : "POST";

                var formData = $(this).serialize();

                $.ajax({
                    type: isEdit ? "PUT" : "POST",
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#addRoleModal').modal('toggle');

                        toastr['success'](response.message, {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: $('html').attr('data-textdirection') === 'rtl'
                        });

                        table.ajax.reload();
                        // window.location.href = response.url;
                    },
                    error: function(response) {
                        if (response.responseJSON.errors)
                            printErrorMsg(response.responseJSON.errors)
                    }
                });
            });


            $(document).on('click', '.create-new', function(e) {
                isEdit = false;
                $('#addRoleModal').find('#modal-title').text('Add New Role');

                if ($('#role-id').length) {
                    $('#role-id').remove();
                }

                $('#addRoleModal').find('.modal-body input[name="name"]').val('');
                $('#addRoleModal').find('.modal-body [type="checkbox"]').prop('checked', false);
                $('#addRoleModal').modal('show');
            });


            $(document).on('click', '.btn-edit', function() {
                var roleId = $(this).data('id');
                isEdit = true;
                $('#addRoleModal').find('#modal-title').text('Edit Role');

                if ($('#role-id').length) {
                    $('#role-id').remove();
                }

                $("#addRoleModal").append(
                    `<input type="text" id="role-id" name="id" value='${roleId}' hidden/>
                `);

                $.ajax({
                    type: "GET",
                    url: "{{ route('roles.getDetailRole') }}",
                    data: {
                        id: roleId
                    },
                    success: function(res) {
                        if (res.status) {
                            var roleData = res.data;
                            $('#name').val(roleData.name);

                            $('input[name="permission[]"]').prop('checked', false);

                            roleData.permissions.forEach(function(permission) {
                                $('input[name="permission[]"][value="' + permission.id +
                                        '"]')
                                    .prop('checked', true);
                            });

                            $('#addRoleModal').modal('show');
                        } else {
                            alert("Failed to retrieve role data.");
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("An error occurred: " + error);
                    }
                });

                $('#addRoleModal').modal('show');
            });

            // Delete Record
            $(document).on('click', '.delete-record', function() {
                var id = $(this).attr("data-id");

                Swal.fire({
                    title: "Apakah anda yakin?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Hapus!",
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-outline-danger ms-1",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        var url = "{{ route('roles.destroy', ':id') }}";
                        url = url.replace(':id', id);

                        $.ajax({
                            type: "DELETE",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id
                            },
                            success: function(response) {
                                toastr['success'](response.message, {
                                    closeButton: true,
                                    tapToDismiss: false,
                                    rtl: $('html').attr(
                                        'data-textdirection') === 'rtl'
                                });

                                table.ajax.reload();
                            },
                            error: function(response) {
                                if (response.responseJSON.errors)
                                    printErrorMsg(response.responseJSON.errors)

                                if (response.responseJSON.message) {
                                    toastr['error'](response.responseJSON.message,
                                        'Error!', {
                                            closeButton: true,
                                            tapToDismiss: false,
                                            rtl: $('html').attr(
                                                'data-textdirection') === 'rtl'
                                        });
                                }
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.detail-record', function() {
                var id = $(this).attr("data-id");
                var url = "{{ route('roles.show', ':id') }}";
                url = url.replace(':id', id);
                window.location.href = url;
            });


            function printErrorMsg(msg) {
                $.each(msg, function(key, value) {
                    var input = $('input[name=' + key + ']');
                    if (input.length > 0) {
                        input.addClass('is-invalid');
                        setTimeout(() => {
                            input.removeClass('is-invalid');
                        }, 5000);
                    }

                    toastr['error'](value,
                        'Error!', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: $('html').attr('data-textdirection') === 'rtl'
                        });
                });
            }
        });
    </script>
@endpush
