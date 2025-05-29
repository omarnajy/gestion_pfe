<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Gestion PFE') - Administration</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE adapté pour Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">

    <!-- Style unifié -->
    <link href="{{ asset('css/unified.css') }}" rel="stylesheet">

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
        .bg-danger,
        .bg-info,
        .bg-success {
            color: #fff !important;
        }

        .bg-warning .small-box-footer,
        .bg-danger .small-box-footer,
        .bg-info .small-box-footer,
        .bg-success .small-box-footer {
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

        /* Fixes pour les graphiques - éviter les conflits avec AdminLTE */
        .chart-container {
            position: relative;
            width: 100%;
            height: auto;
            min-height: 300px;
        }

        .chart-container canvas {
            max-width: 100% !important;
            height: auto !important;
            min-height: 300px !important;
        }

        /* Assurer que les graphiques ne sont pas masqués */
        .content-wrapper .chart-container,
        .content-wrapper .chart-container canvas {
            z-index: 1;
        }

        /* Éviter les conflits de styles pour les graphiques */
        .chart-container * {
            box-sizing: content-box;
        }

        /* Style amélioré pour les small-box - affichage vertical */
        .small-box {
            border-radius: 0.5rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .small-box .inner {
            padding: 15px;
            text-align: center;
        }

        .small-box .inner h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 10px 0;
            white-space: nowrap;
            color: white;
        }

        .small-box .inner p {
            font-size: 1rem;
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .small-box .icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.2) !important;
        }

        .small-box .small-box-footer {
            position: relative;
            text-align: center;
            padding: 5px 0;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            z-index: 10;
            background: rgba(0, 0, 0, 0.1);
            display: block;
            transition: all 0.3s ease;
        }

        .small-box .small-box-footer:hover {
            color: #fff;
            background: rgba(0, 0, 0, 0.15);
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
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Accueil</a>
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
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light ms-3">Gestion PFE</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Tableau de bord</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Gestion des utilisateurs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.projects.index') }}"
                                class="nav-link {{ request()->routeIs('admin.projects*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Supervision des projets</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.evaluations.index') }}"
                                class="nav-link {{ request()->routeIs('admin.evaluations*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-star"></i>
                                <p>Évaluations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.assignments') }}"
                                class="nav-link {{ request()->routeIs('admin.assignments*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-plus"></i>
                                <p>Affectations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.statistics') }}"
                                class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Statistiques</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.defenses.index') }}"
                                class="nav-link {{ request()->routeIs('admin.defenses*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-gavel"></i>
                                <p>Soutenances</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.profile') }}"
                                class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Mon profil</p>
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
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title')</h1>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                    @yield('admin-content')
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

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Stacked Scripts -->
    @stack('scripts')

    @yield('scripts')
</body>

</html>
