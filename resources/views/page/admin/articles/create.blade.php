@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Article/</span> Create</h4>
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Article</h5>
                    <small class="text-muted float-end">Default label</small>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="add-new pt-0 row g-2" id="form-article"
                        onsubmit="return false">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="Judul artikel" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="basic-default-message">Content</label>
                            <textarea id="content" name="content" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="select2-ajax">Category</label>
                            <div class="mb-1 input-group" id="select2-ajax-category2">
                                <select class="select2-data-ajax form-control dt-roles" name="category"
                                    id="select2-ajax-category">
                                    <option value=''>-- Select Category --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="dropzone-basic">Image</label>
                            <input name="image" type="file" class="form-control" id="image" />
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            $("#select2-ajax-category").select2({
                dropdownParent: $("#select2-ajax-category2"),
                ajax: {
                    url: "{{ route('articles.get-category') }}",
                    type: "GET",
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

            $('#form-article').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                var url = "{{ route('articles.store') }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (response) => {
                        toastr['success'](response.message, {
                            closeButton: true,
                            tapToDismiss: false,
                            positionClass: "toast-top-left",
                            rtl: $('html').attr('data-textdirection') === 'rtl'
                        });

                        window.location.href = "{{ route('articles.index') }}";
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

    <script>
        ClassicEditor
            .create(document.querySelector('#content'), {
                ckfinder: {
                    uploadUrl: '{{ route('articles.upload') . '?_token=' . csrf_token() }}'
                }
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
