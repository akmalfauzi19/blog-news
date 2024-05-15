@extends('layouts.main')


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-2">Category List</h4>

        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-article table border-top">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Title</th>
                                    <th>Thumbnail</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!--/ article Table -->
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            let isEdit = false;
            var table = $('.datatables-article').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('articles.list') }}",
                columns: [{
                        data: 'empty'
                    },
                    {
                        data: 'rownum'
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'thumbnail'
                    },
                    {
                        data: 'status'
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
                        targets: 3,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            return (
                                '<img src="' + data +
                                '" alt="" style="width: 150px; height: 150px;">'
                            );
                        }
                    },
                    {
                        targets: 4,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            const status = data !== 'draft' ? 'checked' : '';
                            return (
                                '<label class="switch">' +
                                '<input type="checkbox" id="article_status" ' +
                                status + ' data-id="' + full.action +
                                '" class="switch-input" />' +
                                '<span class="switch-toggle-slider">' +
                                '<span class="switch-on"></span>' +
                                '<span class="switch-off"></span>' +
                                '</span>' +
                                '<span class="switch-label">' + data + '</span>' +
                                '</label>'
                            );
                        }
                    },
                    {
                        // Actions
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return '<a href="javascript:;" class="btn btn-sm btn-icon btn-secondary btn-edit" data-id="' +
                                data + '"><i class="bx bx-edit"></i></a>' +
                                '&nbsp' +
                                '<a href="javascript:;" class="btn btn-sm btn-icon btn-danger delete-record" data-id="' +
                                data + '"><i class="bx bx-trash"></i></a>';
                        }
                    }
                ],
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                order: [
                    [2, 'desc']
                ],
                displayLength: 7,
                lengthMenu: [7, 10, 25, 50, 75, 100],
                buttons: [{
                    text: '<i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Create Article</span>',
                    className: 'create-new btn btn-primary'
                }],
            });

            $('div.head-label').html('<h5 class="card-title mb-0">Table Article</h5>');

            // create-new
            $(document).on('click', '.create-new', function() {
                var url = "{{ route('articles.create') }}";
                window.location.href = url;
            });

            $(document).on('click', '.btn-edit', function() {
                const id = $(this).attr("data-id");
                var url = "{{ route('articles.edit', ':id') }}";
                url = url.replace(':id', id);
                window.location.href = url;
            });

            $(document).on('change', '#article_status', function(e) {
                e.preventDefault();

                const id = $(this).attr("data-id");
                var url = "{{ route('articles.update-status', ':id') }}";
                url = url.replace(':id', id);

                if ($(this).is(':checked')) {
                    $(this).attr('value', 'true');
                } else {
                    $(this).attr('value', 'false');
                }

                const status = $('#article_status').val()

                $.ajax({
                    type: "PATCH",
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id,
                        status
                    },
                    success: function(response) {

                        toastr['success'](response.message, {
                            closeButton: true,
                            tapToDismiss: false,
                            positionClass: "toast-top-left",
                            rtl: $('html').attr('data-textdirection') === 'rtl'
                        });

                        table.ajax.reload();
                    },
                    error: function(response) {
                        if (response.responseJSON.errors)
                            printErrorMsg(response.responseJSON.errors)
                    }
                });

            });

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
                        var url = "{{ route('articles.destroy', ':id') }}";
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
                })
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
        })
    </script>
@endpush
