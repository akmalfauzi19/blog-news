   <!-- Navbar -->

   <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
       id="layout-navbar">
       <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
           <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
               <i class="bx bx-menu bx-sm"></i>
           </a>
       </div>

       <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
           @php
               $roleArr = Auth::user()->getRoleNames()->toArray() ?? [];
           @endphp
           <ul class="navbar-nav flex-row align-items-center ms-auto">
               <!-- User -->
               <li class="nav-item navbar-dropdown dropdown-user dropdown">
                   <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                       <div class="avatar avatar-online">
                           <img src="https://w7.pngwing.com/pngs/184/113/png-transparent-user-profile-computer-icons-profile-heroes-black-silhouette-thumbnail.png"
                               alt class="w-px-40 h-auto rounded-circle" />
                       </div>
                   </a>
                   <ul class="dropdown-menu dropdown-menu-end">
                       <li>
                           <a class="dropdown-item" href="pages-account-settings-account.html">
                               <div class="d-flex">
                                   <div class="flex-shrink-0 me-3">
                                       <div class="avatar avatar-online">
                                           <img src="https://w7.pngwing.com/pngs/184/113/png-transparent-user-profile-computer-icons-profile-heroes-black-silhouette-thumbnail.png"
                                               alt class="w-px-40 h-auto rounded-circle" />
                                       </div>
                                   </div>

                                   <div class="flex-grow-1">
                                       <span class="fw-semibold d-block"> {{ Auth::user()->name }}</span>
                                       <small class="text-muted">
                                           {{ count($roleArr) ? implode('', $roleArr) : $roleArr }}</small>
                                   </div>
                               </div>
                           </a>
                       </li>
                       <li>
                           <div class="dropdown-divider"></div>
                       </li>
                       <li>
                           <a class="dropdown-item" href="pages-profile-user.html">
                               <i class="bx bx-user me-2"></i>
                               <span class="align-middle">My Profile</span>
                           </a>
                       </li>
                       <li>
                           <a class="dropdown-item" href="pages-account-settings-account.html">
                               <i class="bx bx-cog me-2"></i>
                               <span class="align-middle">Settings</span>
                           </a>
                       </li>
                       <li>
                           <div class="dropdown-divider"></div>
                       </li>
                       <li>
                           <button class="dropdown-item" id="keluar">
                               <i class="bx bx-power-off me-2"></i>
                               {{ __('Logout') }}
                           </button>
                       </li>
                   </ul>
               </li>
               <!--/ User -->
           </ul>
       </div>
   </nav>

   <!-- / Navbar -->


   @push('scripts')
       <script>
           // Confirm Login
           $(document).on('click', '#keluar', function(e) {
               Swal.fire({
                   title: "Apakah anda yakin?",
                   text: "Kamu ingin keluar dari sini!",
                   icon: "warning",
                   showCancelButton: true,
                   confirmButtonText: "Yes, Keluar!",
                   customClass: {
                       confirmButton: "btn btn-primary",
                       cancelButton: "btn btn-outline-danger ms-1",
                   },
                   buttonsStyling: false,
               }).then(function(result) {
                   if (result.value) {
                       var data = {
                           '_token': '{{ csrf_token() }}'
                       };
                       $.ajax({
                           type: "POST",
                           url: "{{ route('logout') }}",
                           data: data,
                           dataType: 'json',
                           success: function(response) {
                               Swal.fire({
                                   icon: "success",
                                   title: "Keluar!",
                                   text: "Telah Keluar.",
                                   customClass: {
                                       confirmButton: "btn btn-success",
                                   },
                               });
                               window.location.href = "{{ route('login') }}";
                           }
                       });
                   }
               });
           });
       </script>
   @endpush
