<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Gestion PFE</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE adapté pour Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">

    <!-- Styles personnalisés -->
    <style>
        :root {
            --sidebar-bg: #343a40;
            --sidebar-text: #fff;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --sidebar-active: #0d6efd;
        }

        body {
            font-family: 'Source Sans Pro', 'Poppins', sans-serif;
        }

        /* Styles unifiés pour le sidebar */
        .main-sidebar {
            background-color: var(--sidebar-bg);
            box-shadow: 0 2px 5px 0 rgba(0, 0, 0, .16), 0 2px 10px 0 rgba(0, 0, 0, .12);
        }

        .nav-sidebar .nav-link {
            color: var(--sidebar-text);
            font-weight: 500;
            padding: .75rem 1rem;
            margin-bottom: .2rem;
        }

        .nav-sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
        }

        .nav-sidebar .nav-link.active {
            background-color: var(--sidebar-active);
            color: white;
        }

        /* Correction pour les éléments AdminLTE */
        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.8125rem 0.5rem;
        }

        .small-box .icon {
            color: rgba(0, 0, 0, 0.15);
            z-index: 0;
        }

        .bg-warning,
        .bg-danger {
            color: #fff !important;
        }

        .bg-warning .small-box-footer,
        .bg-danger .small-box-footer {
            color: #fff !important;
        }

        /* Correctifs pour Bootstrap 5 */
        .ml-auto {
            margin-left: auto !important;
        }

        .mr-2 {
            margin-right: 0.5rem !important;
        }

        .ml-3 {
            margin-left: 1rem !important;
        }

        /* Amélioration pour la compatibilité des dropdown */
        .dropdown-menu-right {
            right: 0;
            left: auto;
        }

        /* Harmoniser le header avec les autres templates */
        .main-header {
            background-color: #343a40;
            border-bottom: solid 1px #4b545c;
        }

        .main-header .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }

        .main-header .navbar-nav .nav-link:hover {
            color: #fff;
        }
    </style>

    @yield('styles')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('supervisor.dashboard') }}" class="nav-link">Accueil</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <a href="{{ route('logout') }}" class="dropdown-item"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('supervisor.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light ms-3">Gestion PFE</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('supervisor.dashboard') }}"
                                class="nav-link {{ request()->is('supervisor/dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Tableau de bord</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('supervisor.projects.index') }}"
                                class="nav-link {{ request()->is('supervisor/projects*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-project-diagram"></i>
                                <p>Projets</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('supervisor.students.index') }}"
                                class="nav-link {{ request()->is('supervisor/students*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-graduate"></i>
                                <p>Étudiants</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('supervisor.profile') }}"
                                class="nav-link {{ request()->is('supervisor/profile') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-end d-none d-sm-block">
                <b>Version</b> 1.0
            </div>
            <strong>&copy; {{ date('Y') }} Gestion PFE.</strong> Tous droits réservés.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    @yield('scripts')
</body>

</html>
