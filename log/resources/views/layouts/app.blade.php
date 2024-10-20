<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Finestopia - {{ config('app.name', 'PFMS') }}</title>
    <meta name="description" content="Finestopia - Your Personal Finance Management System. Manage your finances with ease and precision.">
    <meta name="keywords" content="Finestopia, personal finance, money management, budgeting, financial planning">

    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon" />

    {{-- Open Graph meta tags for better social media sharing --}}
    <meta property="og:title" content="Finestopia - Personal Finance Management System">
    <meta property="og:description" content="Manage your finances with ease and precision using Finestopia.">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:url" content="{{ url('/') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;600&display=swap" rel="stylesheet" />

    {{-- Bootstrap CDN --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" />

    {{-- Font-awesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/accounting.js/0.4.1/accounting.min.js"></script>

    {{-- Styles --}}
    @yield('style')
    <link href="{{ asset('css/dev.min.css') }}" rel="stylesheet" />
</head>

<body>

    {{-- Sidebar close button --}}
    <div class="sidebar-close text-center" id="sidebar-close">
        <i class="fa fa-chevron-left"></i>
    </div>
    {{-- Vertical navbar --}}
    @include('layouts.sidebar-new')
    {{-- End vertical navbar --}}

    {{-- Main page content --}}
    <main class="page-content px-2 py-5" id="content">
        <button id="sidebarCollapse" type="button" class="btn btn-menu shadow-sm px-4 mb-2 rounded-0">
            <i class="fa fa-bars mr-2"></i>
            <small class="text-uppercase font-weight-bold">Menu</small>
        </button>
        @yield('content')
    </main>
    {{-- End main page content --}}

    <!-- Jquery, Popeprjs and Bootstrap CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

    @yield('script')

    <!-- Custom Js -->
    <script src="{{ asset('js/custom.js') }}"></script>
    
</body>

</html>