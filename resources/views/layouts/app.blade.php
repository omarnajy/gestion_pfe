<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestion PFE') }} - @yield('title')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Style unifié -->
    <link href="{{ asset('css/unified.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    <div class="min-vh-100 d-flex flex-column">
        <header class="bg-white shadow-sm">
            <div class="container py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <h1 class="h4 mb-0 text-dark">Gestion PFE</h1>
                        </a>
                    </div>
                    <div>
                        @auth
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                    @php
                                    $user = Auth::user();
                                    @endphp

                                    @if($user->role === 'student')
                                       <li><a class="dropdown-item" href="{{ route('student.profile') }}">Profil</a></li>
                                    @elseif($user->role === 'supervisor')
                                       <li><a class="dropdown-item" href="{{ route('supervisor.profile') }}">Profil</a></li>
                                    @elseif($user->role === 'admin')
                                       <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profil</a></li>
                                    @endif
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Connexion</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-grow-1 py-4">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="py-3 bg-light mt-auto">
            <div class="container">
                <p class="text-center text-muted mb-0">
                    © {{ date('Y') }} Gestion PFE. Tous droits réservés.
                </p>
            </div>
        </footer>
    </div>
    
    <!-- JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>