<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion {{ ucfirst($role) }} - Gestion PFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-form {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .role-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <div class="text-center mb-4">
            <div class="role-icon">
                @if($role == 'student')
                    <i class="bi bi-mortarboard"></i>
                @elseif($role == 'supervisor')
                    <i class="bi bi-person-workspace"></i>
                @elseif($role == 'admin')
                    <i class="bi bi-person-gear"></i>
                @endif
            </div>
            <h2>Connexion {{ ucfirst($role) }}</h2>
            <p class="text-muted">Entrez vos identifiants pour accéder à votre espace</p>
        </div>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">
            
            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </div>
        </form>
        
        <div class="mt-3 text-center">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Retour à la sélection des rôles
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>