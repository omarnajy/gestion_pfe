<!-- resources/views/auth/choose-role.blade.php -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir un rôle - Gestion PFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .role-card {
            transition: transform 0.3s;
            cursor: pointer;
            max-width: 300px;
            margin: 0 15px;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
            background-color: #f1f1f1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container text-center">
        <h1 class="mb-5">Gestion des Projets de Fin d'Études</h1>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="card role-card" onclick="window.location.href='{{ route('login.form', 'student') }}'">
                    <div class="card-img-top">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Étudiant</h5>
                        <p class="card-text">Accéder à votre espace étudiant pour gérer votre projet de fin d'études.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card role-card" onclick="window.location.href='{{ route('login.form', 'supervisor') }}'">
                    <div class="card-img-top">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Encadreur</h5>
                        <p class="card-text">Accéder à votre espace encadreur pour superviser les projets de vos
                            étudiants.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card role-card" onclick="window.location.href='{{ route('login.form', 'admin') }}'">
                    <div class="card-img-top">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Administrateur</h5>
                        <p class="card-text">Accéder à votre espace administrateur pour gérer l'ensemble du système.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>

</html>
