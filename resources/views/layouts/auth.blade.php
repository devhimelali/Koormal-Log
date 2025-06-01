<!doctype html>
<html lang="en" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable"
      data-theme="default" data-topbar="light" data-bs-theme="light">
<head>

    <meta charset="utf-8">
    <title>@yield('title') - {{env('APP_NAME')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Minimal Admin & Dashboard Template" name="description">
    <meta content="Themesbrand" name="author">
    <script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Fonts css load -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link id="fontsLink"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap"
          rel="stylesheet">

    <!-- Layout config Js -->
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/libs/toastr/toastr.min.css') }}">
    @yield('page-style')
</head>

<body>


<section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            @yield('content')
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>

<!-- JAVASCRIPT -->
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>

<script src="{{asset('assets/js/pages/password-addon.init.js')}}"></script>

<script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
@yield('page-script')

<script>
    @if (Session::has('success'))
    notify('success', "{{ session('success') }}");
    @elseif (Session::has('error'))
    notify('error', "{{ Session::get('error') }}");
    @elseif (Session::has('warning'))
    notify('warning', "{{ Session::get('warning') }}");
    @elseif (Session::has('info'))
    notify('info', "{{ Session::get('info') }}");
    @endif

    @foreach (session('toasts', collect())->toArray() as $toast)
    const options = {
        title: '{{ $toast['title'] ?? '' }}',
        message: '{{ $toast['message'] ?? 'No message provided' }}',
        position: '{{ $toast['position'] ?? 'topRight' }}',
    };
    show('{{ $toast['type'] ?? 'info' }}', options);
    @endforeach

    function notify(type, msg, position = 'topRight') {
        toastr[type](msg);
    }

    function show(type, options) {
        if (['info', 'success', 'warning', 'error'].includes(type)) {
            toastr[type](options);
        } else {
            toastr.show(options);
        }
    }
</script>
@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script>
            notify('error', "{{ $error }}");
        </script>
    @endforeach
@endif
</body>

</html>
