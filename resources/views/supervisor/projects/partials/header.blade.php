<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Gestion PFE') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @auth
                    @if(auth()->user()->role === 'student')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.projects*') ? 'active' : '' }}" href="{{ route('student.projects') }}">
                                <i class="fas fa-project-diagram"></i> Mes projets
                            </a>
                        </li>
                    @elseif(auth()->user()->role === 'supervisor')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('supervisor.students*') ? 'active' : '' }}" href="{{ route('supervisor.students') }}">
                                <i class="fas fa-user-graduate"></i> Étudiants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('supervisor.projects*') ? 'active' : '' }}" href="{{ route('supervisor.projects') }}">
                                <i class="fas fa-project-diagram"></i> Projets
                            </a>
                        </li>
                    @elseif(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users"></i> Utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.projects*') ? 'active' : '' }}" href="{{ route('admin.projects') }}">
                                <i class="fas fa-project-diagram"></i> Projets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.assignments*') ? 'active' : '' }}" href="{{ route('admin.assignments') }}">
                                <i class="fas fa-user-check"></i> Affectations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.statistics*') ? 'active' : '' }}" href="{{ route('admin.statistics') }}">
                                <i class="fas fa-chart-bar"></i> Statistiques
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.documents*') ? 'active' : '' }}" href="{{ route('admin.documents') }}">
                                <i class="fas fa-file-alt"></i> Documents
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">{{ __('Connexion') }}</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a id="notificationsDropdown" class="nav-link" href="{{ route('notifications.index') }}" role="button">
                            <i class="fas fa-bell"></i>
                            @if(auth()->user()->unreadNotificationsCount() > 0)
                                <span class="badge badge-danger">{{ auth()->user()->unreadNotificationsCount() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> {{ __('Déconnexion') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>