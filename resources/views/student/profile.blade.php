@extends('layouts.student')

@section('title', 'Mon Profil')

@section('page-title', 'Mon Profil')

@section('student-content')
<div class="row">
    <!-- Informations personnelles -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
               <h3 class="card-title">Informations personnelles</h3>
               <div class="card-tools">
                  <a href="{{ route('student.profile.edit') }}" class="btn btn-tool">
                     <i class="fas fa-edit"></i>
                  </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th style="width: 30%">Nom complet</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Adresse e-mail</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Numéro étudiant</th>
                            <td>{{ $user->student_id ?? 'Non défini' }}</td>
                        </tr>
                        <tr>
                            <th>Filière</th>
                            <td>{{ $user->field ?? 'Non défini' }}</td>
                        </tr>
                        <tr>
                            <th>Date d'inscription</th>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sécurité du compte -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sécurité du compte</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('student.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="current_password">Mot de passe actuel</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour éditer le profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Modifier mon profil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('student.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone ?? '' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Pour afficher les messages de succès ou d'erreur
        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
        
        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    });
</script>
@endsection