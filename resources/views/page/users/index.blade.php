@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            Management User
        </h4>

        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-user table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- Modal to add new record -->
        <div class="offcanvas offcanvas-end" id="add-new-record">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body flex-grow-1">
                <form class="add-new-record pt-0 row g-2" id="form-user" onsubmit="return false">
                    @csrf
                    <div class="col-sm-12">
                        <label class="form-label" for="basicFullname">Name</label>
                        <div class="input-group input-group-merge">
                            <span id="basicFullname2" class="input-group-text">
                                <i class="bx bx-user"></i>
                            </span>
                            <input type="text" id="name" class="form-control dt-name" name="name"
                                placeholder="John Doe" aria-label="John Doe" />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                            <input type="text" id="email" name="email" class="form-control dt-email"
                                placeholder="john.doe@example.com" aria-label="john.doe@example.com" />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label" for="basicFullname">Password</label>
                        <div class="input-group input-group-merge">
                            <span id="password" class="input-group-text">
                                <i class="bx bx-lock"></i>
                            </span>
                            <input type="password" id="password" placeholder="password" class="form-control dt-password"
                                name="password" aria-describedby="password" />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label" for="basicFullname">Password Confirmation</label>
                        <div class="input-group input-group-merge">
                            <span id="password_confirm" class="input-group-text">
                                <i class="bx bx-lock"></i>
                            </span>
                            <input type="password" id="password-confirm" class="form-control dt-password-confirm"
                                name="password_confirm" placeholder="password confirm"
                                aria-describedby="password_confirm" />
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="select2-ajax">Role User</label>
                        <div class="mb-1 input-group" id="select2-ajax-roles2">
                            <select class="select2-data-ajax form-control dt-roles" name="roles" id="select2-ajax-roles">
                                <option value=''>-- Select Role --</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            let isEdit = false;
            var table = $('.datatables-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.list') }}",
                columns: [{
                        data: 'empty'
                    },
                    {
                        data: 'rownum'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'roles'
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
                    },
                    {
                        targets: 4,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var roles = data.map((role, i) => {
                                return `<span class="badge rounded-pill bg-primary">${role}</span>`
                            })

                            return roles;
                        }
                    },
                    {
                        // Actions
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                '<div class="d-inline-block">' +
                                '<a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-end m-0">' +
                                '<li><a href="javascript:;" class="dropdown-item detail-record" data-id="' +
                                data + '">Details</a></li>' +
                                '<div class="dropdown-divider"></div>' +
                                '<li><a href="javascript:;" class="dropdown-item text-danger delete-record" data-id="' +
                                data +
                                '">Delete</a></li>' +
                                '</ul>' +
                                '</div>' +
                                '<a href="javascript:;" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit"></i></a>'
                            );
                        }
                    }
                ],
                order: [
                    [2, 'desc']
                ],
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 7,
                lengthMenu: [7, 10, 25, 50, 75, 100],
                buttons: [{
                    text: '<i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
                    className: 'create-new btn btn-primary'
                }],
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'Details of ' + data['full_name'];
                            }
                        }),
                        type: 'column',
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                return col.title !==
                                    '' // ? Do not show row in modal popup if title is blank (for check box)
                                    ?
                                    '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) :
                                false;
                        }
                    }
                }
            });

            $('div.head-label').html('<h5 class="card-title mb-0">Table User</h5>');

            // new record
            // setTimeout(() => {
            const newRecord = $('.create-new');
            const offCanvasElement = $('#add-new-record');

            if (newRecord.length) {
                newRecord.on('click', function() {
                    isEdit = false;
                    $('#exampleModalLabel').text('New User');
                    if ($('#userId').length) {
                        $('#userId').remove();
                    }

                    const offCanvasEl = new bootstrap.Offcanvas(offCanvasElement.get(0));
                    // Empty fields on offCanvas open
                    offCanvasElement.find('.dt-name').val('');
                    offCanvasElement.find('.dt-email').val('');
                    offCanvasElement.find('.dt-password').val('');
                    offCanvasElement.find('.dt-password-confirm').val('');
                    $('#select2-ajax-roles').select2("val", "");

                    // Open offCanvas with form
                    offCanvasEl.show();
                });
            }
            // }, 200);

            // edit
            $('.datatables-user').on('click', '.item-edit', function() {
                isEdit = true;
                var rowData = table.row($(this).closest('tr')).data();

                const id = rowData.action;

                if ($('#userId').length) {
                    $('#userId').remove();
                }
                $("#form-user").append(`<input type="text" id="userId" name="id" value='${id}' hidden/>`);

                var url = "{{ route('users.getUser', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        id
                    },
                    success: function(res) {
                        $('#name').val(res.data.user.name);
                        $('#email').val(res.data.user.email);
                        $('#select2-ajax-roles').select2('val', Object.values(res.data
                            .userRole)[0]);

                        var offCanvasEl = new bootstrap.Offcanvas(offCanvasElement.get(0));
                        offCanvasEl.show();
                    },
                    error: function(res) {
                        if (response.responseJSON.errors)
                            printErrorMsg(response.responseJSON.errors)
                    }
                });

                $('#exampleModalLabel').text('Edit User');

                var offCanvasEl = new bootstrap.Offcanvas(offCanvasElement.get(0));
                offCanvasEl.show();
            });

            $(document).on('click', '.detail-record', function() {
                var id = $(this).attr("data-id");
                var url = "{{ route('users.show', ':id') }}";
                url = url.replace(':id', id);
                window.location.href = url;
            });

            // delete
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
                        var url = "{{ route('users.destroy', ':id') }}";
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
                                    positionClass: "toast-top-left",
                                    rtl: $('html')
                                        .attr('data-textdirection') === 'rtl'
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

            $("#select2-ajax-roles").select2({
                dropdownParent: $("#select2-ajax-roles2"),
                ajax: {
                    url: "{{ route('users.getRole') }}",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            search: params.term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });

            $("#form-user").submit(function(e) {
                e.preventDefault();
                var url = "{{ route('users.store') }}";
                if (isEdit) {
                    const id = $('#userId').val();
                    url = "{{ route('users.update', ':id') }}";
                    url = url.replace(':id', id);
                }

                var formData = $(this).serialize();

                $.ajax({
                    type: isEdit ? "PUT" : "POST",
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#AddUserModal').modal('toggle');

                        toastr['success'](response.message, {
                            closeButton: true,
                            tapToDismiss: false,
                            positionClass: "toast-top-left",
                            rtl: $('html').attr('data-textdirection') === 'rtl'
                        });

                        var offCanvasEl = new bootstrap.Offcanvas(offCanvasElement.get(0));
                        offCanvasEl.hide();

                        table.ajax.reload();
                    },
                    error: function(response) {
                        if (response.responseJSON.errors)
                            printErrorMsg(response.responseJSON.errors)
                    }
                });
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
                            positionClass: "toast-top-left",
                            rtl: $('html').attr('data-textdirection') === 'rtl'
                        });
                });
            }
        });
    </script>
@endpush
