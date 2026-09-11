<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FontAwesome JS-->
    <script defer src="{{ asset('assets/backend/plugins/fontawesome/js/all.min.js') }}"></script>

    <!-- App CSS -->
    <link id="theme-style" rel="stylesheet" href="{{ asset('assets/backend/css/portal.css') }}">
</head>

<body class="app app-login p-0">

    @yield('content')

    <!-- Javascript -->
    <script src="{{ asset('assets/backend/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Page Specific JS -->
    <script src="{{ asset('assets/backend/js/app.js') }}"></script>
    @yield('scripts')

</body>

</html>