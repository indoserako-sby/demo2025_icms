<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template-no-customizer">
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@stack('title')</title>

    <meta name="description" content="" />

    @include('layouts.style.style')
    @stack('style')
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> --}}
    {{-- @stack('style') --}}
    @livewireStyles

</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.sidebar.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.navbar.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('content')


                    <!-- Footer -->
                    @include('layouts.footer.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>

        @if (Session::has('success_message'))
            <script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: '{{ Session::get('success_message') }}'
                });
            </script>
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <script>
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: '{{ $error }}'
                    });
                </script>
            @endforeach
        @endif

        @if (Session::has('error_message_update_details'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ Session::get('error_message_update_details') }}",
                    showConfirmButton: false,
                    timer: 3000 // milliseconds
                });
            </script>
        @endif

        @if (Session::has('error_message_not_found'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ Session::get('error_message_not_found') }}",
                    showConfirmButton: false,
                    timer: 3000 // milliseconds
                });
            </script>
        @endif

        @if (Session::has('error_message_delete'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ Session::get('error_message_delete') }}",
                    showConfirmButton: false,
                    timer: 3000 // milliseconds
                });
            </script>
        @endif

        @if (Session::has('success_message_create'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ Session::get('success_message_create') }}",
                    showConfirmButton: false,
                    timer: 3000 // milliseconds
                });
            </script>
        @endif

        @if (Session::has('success_message_update'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ Session::get('success_message_update') }}",
                    showConfirmButton: false,
                    timer: 3000 // milliseconds
                });
            </script>
        @endif

        @if (Session::has('success_message_delete'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ Session::get('success_message_delete') }}",
                    showConfirmButton: false,
                    timer: 3000 // milliseconds
                });
            </script>
        @endif
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    @include('layouts.script.script')
    @stack('script')
    @livewireScripts
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}
    {{-- @stack('script') --}}
</body>

</html>
