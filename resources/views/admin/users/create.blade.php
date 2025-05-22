{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.admin')

@section('admin-content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Créer un compte {{ $roleLabel }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.store', $role ?? 'default') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>
                
                @if($role == 'student')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="student_id">Numéro étudiant</label>
                            <input type="text" class="form-control @error('student_id') is-invalid @enderror" id="student_id" name="student_id" value="{{ old('student_id') }}" required>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field">Filière</label>
                            <select class="form-control @error('field') is-invalid @enderror" id="field" name="field" required>
                                <option value="">Sélectionner une filière</option>
                                <option value="informatique" {{ old('field') == 'informatique' ? 'selected' : '' }}>Informatique</option>
                                <option value="reseaux" {{ old('field') == 'reseaux' ? 'selected' : '' }}>Réseaux et Télécommunications</option>
                                <option value="electrique" {{ old('field') == 'electrique' ? 'selected' : '' }}>Génie Électrique</option>
                                <option value="mecanique" {{ old('field') == 'mecanique' ? 'selected' : '' }}>Génie Mécanique</option>
                                <option value="civile" {{ old('field') == 'civile' ? 'selected' : '' }}>Génie Civil</option>
                            </select>
                            @error('field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
                
                @if($role == 'supervisor')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="specialty">Spécialité</label>
                            <input type="text" class="form-control @error('specialty') is-invalid @enderror" id="specialty" name="specialty" value="{{ old('specialty') }}" required>
                            @error('specialty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="department">Département</label>
                            <select class="form-control @error('department') is-invalid @enderror" id="department" name="department" required>
                                <option value="">Sélectionner un département</option>
                                <option value="informatique" {{ old('department') == 'informatique' ? 'selected' : '' }}>Informatique</option>
                                <option value="reseaux" {{ old('department') == 'reseaux' ? 'selected' : '' }}>Réseaux et Télécommunications</option>
                                <option value="electrique" {{ old('department') == 'electrique' ? 'selected' : '' }}>Génie Électrique</option>
                                <option value="mecanique" {{ old('department') == 'mecanique' ? 'selected' : '' }}>Génie Mécanique</option>
                                <option value="civile" {{ old('department') == 'civile' ? 'selected' : '' }}>Génie Civil</option>
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_students">Nombre maximum d'étudiants à encadrer</label>
                            <input type="number" class="form-control @error('max_students') is-invalid @enderror" id="max_students" name="max_students" value="{{ old('max_students', 5) }}" min="1" max="20" required>
                            @error('max_students')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Compte actif</label>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection