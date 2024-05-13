<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../admin/" data-template="vertical-menu-template-no-customizer">

<head>
    @include('layouts.head')
    @stack('styles')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.aside')
            </aside>
            <!-- / Menu -->


            <div class="layout-page">
                @include('layouts.nav')
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    @yield('content')
                    @include('layouts.footer')
                </div>

                <!-- Content wrapper -->
            </div>
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    @include('layouts.js')
    @stack('scripts')
</body>

</html>
