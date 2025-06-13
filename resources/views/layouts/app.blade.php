<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ URL::asset('assets/img/logo.png') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/vendor/DataTables/css/datatables.min.css') }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="{{ URL::asset('assets/fonts/fontawesome-all.min.css') }}">
    <!-- In your <head> -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .bg-gradient-primary {
            background-color: #161b2d !important;
            background-image: linear-gradient(135deg, #161b2d 0%, #27335f 100%);
        }

        .sidebar {
            min-width: 18rem !important;
        }

        /* Ensure nav-link fills full width */
        .nav-item {
            width: 100%;
        }

        .nav-link {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            color: #ffffff;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
            border-radius: 0;
            /* Or keep rounded if you prefer */
        }

        /* Active state */
        .nav-link.active {
            background-color: rgb(39, 51, 95);
            color: #ffffff !important;
            font-weight: bold;
            border-left: 4px solid #ffffff;
            padding-left: calc(1rem - 4px);
        }

        /* Hover effect */
        .nav-link:hover {
            background-color: rgba(39, 51, 95, .5);
            color: #ffffff !important;
        }
    </style>

</head>

@stack('css')

<body id="page-top">
    <div id="wrapper">
        @include('sweetalert::alert')

        @include('components.navigation')

        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-light navbar-expand topbar static-top mb-4 bg-white shadow">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3"
                            id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <div class="navbar-nav ms-auto flex-nowrap">
                            <div class="nav-item dropdown no-arrow">
                                <div class="card border bg-dark text-white py-1 px-3">
                                    <span class="text-uppercase">{{ auth()->user()->role }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                <main>
                    @yield('content')
                </main>
            </div>
            <footer class="sticky-footer mt-3 bg-white">
                <div class="container my-auto">
                    <div class="copyright my-auto text-center"><span>Copyright ©
                            {{ config('app.name') }}
                            {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <a class="d-inline scroll-to-top rounded border" href="#page-top"><i class="fas fa-angle-up"></i></a>
        </div>
    </div>
    <script src="{{ URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/script.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/DataTables/js/datatables.min.js') }}"></script>
    <!-- Before </body> -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    @include('components.script-datatable')
    @include('components.script-select2')

    @stack('scripts')

</body>

</html>
