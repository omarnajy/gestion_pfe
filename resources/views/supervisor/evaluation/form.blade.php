{{-- resources/views/supervisor/evaluation/form.blade.php --}}
@extends('layouts.supervisor')

@section('title', 'Évaluer le projet')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Évaluation : {{ $project->title }}</h5>
                        <a href="{{ route('supervisor.projects.show', $project->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au projet
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Informations du projet</h6>
                                <p><strong>Étudiant :</strong> {{ $project->student->name }}</p>
                                <p><strong>Email :</strong> {{ $project->student->email }}</p>
                                <p><strong>Statut :</strong> <span
                                        class="badge bg-{{ $project->status_color }}">{{ $project->status_text }}</span></p>
                            </div>
                            <div class="col-md-6">
                                @if ($existingEvaluation)
                                    <div class="alert alert-warning">
                                        <strong>Évaluation existante :</strong>
                                        {{ number_format($existingEvaluation->grade, 2) }}/20
                                        <br><small>Dernière modification :
                                            {{ $existingEvaluation->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('supervisor.evaluation.store', $project->id) }}" method="POST">
                            @csrf

                            <div class="row mb-3">

                                <div class="col-md-6">
                                    <label for="presentation_grade" class="form-label">Note Présentation <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('presentation_grade') is-invalid @enderror"
                                        id="presentation_grade" name="presentation_grade" min="0" max="20"
                                        step="0.25"
                                        value="{{ old('presentation_grade', $existingEvaluation->presentation_grade ?? '') }}"
                                        required>
                                    <small class="text-muted">Sur 20 points</small>
                                    @error('presentation_grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="documentation_grade" class="form-label">Note Documentation <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('documentation_grade') is-invalid @enderror"
                                        id="documentation_grade" name="documentation_grade" min="0" max="20"
                                        step="0.25"
                                        value="{{ old('documentation_grade', $existingEvaluation->documentation_grade ?? '') }}"
                                        required>
                                    <small class="text-muted">Sur 20 points</small>
                                    @error('documentation_grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ $existingEvaluation ? 'Mettre à jour' : 'Enregistrer' }}
                                    l'évaluation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calcul automatique de la moyenne
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = ['technical_grade', 'presentation_grade', 'documentation_grade'];

            inputs.forEach(id => {
                document.getElementById(id).addEventListener('input', calculateAverage);
            });

            function calculateAverage() {
                const technical = parseFloat(document.getElementById('technical_grade').value) || 0;
                const presentation = parseFloat(document.getElementById('presentation_grade').value) || 0;
                const documentation = parseFloat(document.getElementById('documentation_grade').value) || 0;

                const average = (technical + presentation + documentation) / 3;

                // Afficher la moyenne quelque part si nécessaire
                console.log('Moyenne:', average.toFixed(2));
            }
        });
    </script>
@endsection
