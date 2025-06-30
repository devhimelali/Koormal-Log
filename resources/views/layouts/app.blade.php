<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark"
    data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-topbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_DESCRIPTION') }}" name="description">
    <meta content="{{ env('APP_NAME') }}" name="author">
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Fonts css load -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link id="fontsLink"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/libs/toastr/toastr.min.css') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        /* loader css */
        #ajaxLoaderOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-container {
            display: flex;
            gap: 15px;
        }

        .dot {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #ff4500;
            animation: bounce 1.2s infinite ease-in-out;
        }

        .dot1 {
            animation-delay: 0s;
        }

        .dot2 {
            animation-delay: 0.2s;
        }

        .dot3 {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        /* end loader css */
    </style>
    @yield('vendor-style')
    @yield('page-style')
    @stack('styles')
</head>

<body>
    <!-- Global AJAX Loader -->
    <div id="ajaxLoaderOverlay" style="display: none;">
        <div class="loader-container">
            <div class="dot dot1"></div>
            <div class="dot dot2"></div>
            <div class="dot dot3"></div>
        </div>
    </div>


    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('redirect') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="22">
                    </span>
                </a>
                <a href="{{ route('redirect') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="22">
                    </span>
                </a>
                <button type="button" class="p-0 btn btn-sm fs-3xl header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                @hasrole('admin')
                    @include('admin.partials.sidebar')
                @endhasrole
                @hasrole('supervisor')
                    @include('supervisor.partials.sidebar')
                @endhasrole
                @hasrole('user')
                    @include('user.partials.sidebar')
                @endhasrole
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>
        <header id="page-topbar">
            <div class="layout-width">
                @include('layouts.partials.topbar')
            </div>
        </header>
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div><!--end row-->
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        @include('layouts.partials.footer')
    </div>
    <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->
    <!--start back-to-top-->
    <button class="btn btn-dark btn-icon" id="back-to-top">
        <i class="bi bi-caret-up fs-3xl"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div id="loader"
        style="display: none; position: fixed; top: 50%; left: 50%;
    transform: translate(-50%, -50%); z-index: 1051; background-color: rgba(208,208,208,0.3); width: 100% ; height: 100%;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <!-- Theme Settings -->
    @include('layouts.partials.theme-setting')

    <script>
        function ajaxBeforeSend(formSelector, buttonSelector) {
            $(formSelector).find('.is-invalid').removeClass('is-invalid');
            $(buttonSelector).prop('disabled', true);
            $(buttonSelector).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
            );
        }

        function handleAjaxErrors(xhr, status, error) {
            switch (xhr.status) {
                case 400:
                    notify('error',
                        'The request could not be processed due to invalid input. Please review your data and try again.'
                    );
                    break;
                case 401:
                    notify('error', 'Your session has expired or you are not logged in. Please log in to continue.');
                    break;
                case 403:
                    notify('error',
                        'You do not have permission to perform this action. Please contact your administrator if you believe this is an error.'
                    );
                    break;
                case 404:
                    notify('error', );
                    message = 'The requested resource could not be found. It may have been moved or deleted.';
                    break;
                case 422:
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        notify('error', value);
                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        if (input.closest('.auth-pass-inputgroup').length) {
                            input.closest('.auth-pass-inputgroup').find('.invalid-feedback').text(value);
                        } else {
                            input.next('.invalid-feedback').text(value);
                        }
                    });
                    break;
                case 429:
                    notify('error', 'Too many requests. Please try again later.');
                    break;
                case 500:
                    notify('error',
                        'An unexpected server error occurred. Please try again later or contact support if the issue persists.'
                    );
                    break;
                case 0:
                    notify('error',
                        'Network connection lost or server is unreachable. Please check your internet connection and try again.'
                    );
                    break;
                default:
                    notify('error', 'An unknown error occurred. Please try again or contact support.');
                    break;
            }
        }

        function ajaxComplete(buttonSelector, defaultText = 'Save') {
            $(buttonSelector).prop('disabled', false);
            $(buttonSelector).html(defaultText);
        }
    </script>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
    @include('layouts.partials.script')
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('vendor-script')
    @yield('page-script')
    @stack('scripts')

</body>

</html>
