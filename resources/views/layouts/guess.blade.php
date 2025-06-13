<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ URL::asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="{{ URL::asset('assets/fonts/fontawesome-all.min.css') }}">
    <style>
        .bg-gradient-primary {
            background-color: #161b2d !important;
            background-image: linear-gradient(135deg, #161b2d 0%, #27335f 100%);
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            height: 100vh;
            width: 100%;
        }
    </style>


</head>

<body class="bg-gradient-primary">
    @include('sweetalert::alert')

    @yield('login')
    <script src="{{ URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/script.min.js') }}"></script>
</body>

</html>
