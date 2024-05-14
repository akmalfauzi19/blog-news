@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <!-- Notifications -->
            <h5 class="card-header">Role name : {{ $role->name }}</h5>

            <div class="table-responsive">
                <table class="table table-striped table-borderless border-bottom">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Permissions : </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $name => $rolePermission)
                            <tr>
                                <td class="text-nowrap">{{ $name }}</td>
                                @foreach ($rolePermission as $role)
                                    <td>
                                        <div class="form-check d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="defaultCheck1"
                                                {{ $role->status ? 'checked' : '' }} disabled />
                                            <label class="form-check-label p-1" for="defaultCheck1">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
