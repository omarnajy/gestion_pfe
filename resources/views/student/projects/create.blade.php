{{-- resources/views/student/projects/create.blade.php --}}
@extends('layouts.student')

@section('student-content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Proposer un projet de PFE</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('student.projects.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à mon projet
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('student.projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if (request('from_project'))
                        <input type="hidden" name="from_project" value="{{ request('from_project') }}">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Vous êtes en train de soumettre à nouveau un projet rejeté.
                            Certains champs ont été pré-remplis.
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du projet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title', $previousProject->title ?? '') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Le titre doit être clair et concis (maximum 100 caractères).</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description du projet <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="5" required>{{ old('description', $previousProject->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Décrivez votre projet en détail, les objectifs, les technologies à
                            utiliser, etc.</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="field" class="form-label">Domaine <span class="text-danger">*</span></label>
                            <select class="form-select @error('field') is-invalid @enderror" id="field" name="field"
                                required>
                                <option value="">Sélectionner un domaine</option>
                                <option value="developpement_web"
                                    {{ old('field', $previousProject->field ?? '') == 'developpement_web' ? 'selected' : '' }}>
                                    Développement Web</option>
                                <option value="developpement_mobile"
                                    {{ old('field', $previousProject->field ?? '') == 'developpement_mobile' ? 'selected' : '' }}>
                                    Développement Mobile</option>
                                <option value="intelligence_artificielle"
                                    {{ old('field', $previousProject->field ?? '') == 'intelligence_artificielle' ? 'selected' : '' }}>
                                    Intelligence Artificielle</option>
                                <option value="securite_informatique"
                                    {{ old('field', $previousProject->field ?? '') == 'securite_informatique' ? 'selected' : '' }}>
                                    Sécurité Informatique</option>
                                <option value="iot"
                                    {{ old('field', $previousProject->field ?? '') == 'iot' ? 'selected' : '' }}>Internet
                                    des Objets (IoT)</option>
                                <option value="science_donnees"
                                    {{ old('field', $previousProject->field ?? '') == 'science_donnees' ? 'selected' : '' }}>
                                    Science des Données</option>
                                <option value="reseaux"
                                    {{ old('field', $previousProject->field ?? '') == 'reseaux' ? 'selected' : '' }}>
                                    Réseaux</option>
                                <option value="systemes_embarques"
                                    {{ old('field', $previousProject->field ?? '') == 'systemes_embarques' ? 'selected' : '' }}>
                                    Systèmes Embarqués</option>
                                <option value="autre"
                                    {{ old('field', $previousProject->field ?? '') == 'autre' ? 'selected' : '' }}>Autre
                                </option>
                            </select>
                            @error('field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="technologies" class="form-label">Technologies utilisées</label>
                            <input type="text" class="form-control @error('technologies') is-invalid @enderror"
                                id="technologies" name="technologies"
                                value="{{ old('technologies', $previousProject->technologies ?? '') }}">
                            @error('technologies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Exemples: Laravel, React, MySQL, Python, TensorFlow, etc.</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Soumettre le projet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
