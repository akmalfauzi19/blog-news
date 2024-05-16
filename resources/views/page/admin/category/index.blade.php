@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-2">Category List</h4>

        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-category table border-top">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Name</th>
                                    @if (!empty(array_intersect(['category-edit', 'category-delete'], auth()->user()->list_role)))
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!--/ category Table -->
            </div>
        </div>
        @if (!empty(array_intersect(['category-edit', 'category-create'], auth()->user()->list_role)))
            <div class="offcanvas offcanvas-end" id="add-new-record">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body flex-grow-1">
                    <form class="add-new-record pt-0 row g-2" id="form-category" onsubmit="return false">
                        @csrf
                        <div class="col-sm-12">
                            <label class="form-label" for="basicFullname">Title Category</label>
                            <div class="input-group input-group-merge">
                                <span id="basicFullname2" class="input-group-text">
                                    <i class="bx bx-category"></i>
                                </span>
                                <input type="text" id="name" class="form-control dt-name" name="name"
                                    placeholder="Sport" aria-label="Sport" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary"
                                data-bs-dismiss="offcanvas">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            let isEdit = false;
            var table = $('.datatables-category').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('categories.list') }}",
                columns: [{
                        data: 'empty'
                    },
                    {
                        data: 'rownum'
                    },
                    {
                        data: 'name'
                    }
                    @if (!empty(array_intersect(['category-edit', 'category-create', 'category-delete'], auth()->user()->list_role)))
                        , {
                            data: 'action'
                        }
                    @endif
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
                    }
                    @if (!empty(array_intersect(['category-edit', 'category-edit'], auth()->user()->list_role)))
                        , {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {

                                // return '<a href="javascript:;" class="btn btn-sm btn-icon btn-secondary btn-edit" data-id="' +
                                //     data + '"><i class="bx bx-edit"></i></a>' +
                                //     '&nbsp' +
                                //     '<a href="javascript:;" class="btn btn-sm btn-icon btn-danger delete-record" data-id="' +
                                //     data + '"><i class="bx bx-trash"></i></a>';

                                let buttons = '';
                                @if (!empty(array_intersect(['category-edit'], auth()->user()->list_role)))
                                    buttons +=
                                        '<a href="javascript:;" class="btn btn-sm btn-icon btn-secondary btn-edit" data-id="' +
                                        data + '"><i class="bx bx-edit"></i></a>';
                                @endif

                                @if (!empty(array_intersect(['category-delete'], auth()->user()->list_role)))
                                    buttons +=
                                        '<a href="javascript:;" class="btn btn-sm btn-icon btn-danger delete-record" data-id="' +
                                        data + '"><i class="bx bx-trash"></i></a>';
                                @endif

                                return buttons;
                            }
                        }
                    @endif
                ],
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                order: [
                    [2, 'desc']
                ],
                displayLength: 7,
                lengthMenu: [7, 10, 25, 50, 75, 100],
                buttons: [{
                    @if (!empty(array_intersect(['category-create'], auth()->user()->list_role)))

                        text: '<i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Create Category</span>',
                        className: 'create-new btn btn-primary'
                    @endif
                }],
            });

            $('div.head-label').html('<h5 class="card-title mb-0">Table Categories Article</h5>');

            // new record
            const newRecord = $('.create-new');
            const offCanvasElement = $('#add-new-record');

            if (newRecord.length) {
                newRecord.on('click', function() {
                    isEdit = false;
                    $('#exampleModalLabel').text('New Category');
                    if ($('#cat-id').length) {
                        $('#cat-id').remove();
                    }

                    const offCanvasEl = new bootstrap.Offcanvas(offCanvasElement.get(0));
                    // Empty fields on offCanvas open
                    offCanvasElement.find('.dt-name').val('');

                    // Open offCanvas with form
                    offCanvasEl.show();
                });
            }

            $('.datatables-category').on('click', '.btn-edit', function() {
                isEdit = true;
                var id = $(this).attr("data-id");

                if ($('#cat-id').length) {
                    $('#cat-id').remove();
                }

                $("#form-category").append(
                    `<input type="text" id="cat-id" name="id" value='${id}' hidden/>`
                );

                $('#exampleModalLabel').text('Edit Category');

                var url = "{{ route('categories.getCategory', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        id
                    },
                    success: function(res) {
                        $('#name').val(res.data.name);

                        $('#form-category').append(
                            `<input type="text" id="cat-id" name="id" value='${res.data.id}' hidden/>
                        `);

                        var offCanvasEl = new bootstrap.Offcanvas(offCanvasElement.get(0));
                        offCanvasEl.show();
                    },
                    error: function(res) {
                        if (response.responseJSON.errors)
                            printErrorMsg(response.responseJSON.errors)
                    }
                });
            });

            $(document).on('submit', '#form-category', function(e) {
                e.preventDefault();
                let url = "{{ route('categories.store') }}";

                if (isEdit) {
                    const id = $('#cat-id').val();
                    url = "{{ route('categories.update', ':id') }}";
                    url = url.replace(':id', id);
                }

                var method = isEdit ? "PUT" : "POST";

                var formData = $(this).serialize();

                $.ajax({
                    type: isEdit ? "PUT" : "POST",
                    url: url,
                    data: formData,
                    success: function(response) {

                        toastr['success'](response.message, {
                            closeButton: true,
                            tapToDismiss: false,
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
                        var url = "{{ route('categories.destroy', ':id') }}";
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
            })

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
        })
    </script>
@endpush
