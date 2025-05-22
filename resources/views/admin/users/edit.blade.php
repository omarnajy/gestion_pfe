@extends('layouts.admin')
@section('title', 'Modifier un utilisateur')
@section('admin-content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Modifier l'utilisateur</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </div>
    </div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Formulaire de modification</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Rôle</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Étudiant</option>
                            <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Encadreur</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrateur</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Biographie</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Nouveau mot de passe (laisser vide pour conserver l'actuel)</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Le mot de passe doit contenir au moins 8 caractères.</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection