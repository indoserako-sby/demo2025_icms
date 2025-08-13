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
    <div class="content-wrapper">
        <!-- Content -->
        @yield('content')


        <!-- Footer -->
        <!-- / Footer -->

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
