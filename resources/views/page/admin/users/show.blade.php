@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User / </span> View </h4>
        <div class="d-flex justify-content-center ">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded my-4"
                                    src="https://w7.pngwing.com/pngs/184/113/png-transparent-user-profile-computer-icons-profile-heroes-black-silhouette-thumbnail.png"
                                    height="110" width="110" alt="User avatar" />
                                <div class="user-info text-center">
                                    <h4 class="mb-2"> {{ $user->name }}</h4>
                                    <span class="badge bg-label-secondary">Author</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-2 border-bottom mb-4">Details</h5>
                        <div class="info-container">
                            <ul class="list-unstyled">

                                <li class="mb-3">
                                    <span class="fw-bold me-2">Email:</span>
                                    <span>{{ $user->email }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Role:</span>
                                    <span>
                                        @if (!empty($user->getRoleNames()))
                                            @foreach ($user->getRoleNames() as $v)
                                                <label class="badge bg-primary">{{ $v }}</label>
                                            @endforeach
                                        @endif
                                    </span>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
            </div>
        </div>
    </div>
@endsection
