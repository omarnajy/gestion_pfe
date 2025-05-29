@extends('layouts.admin')

@section('admin-content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Programmer une Soutenance</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.defenses.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Projet *</label>
                                <select name="project_id" class="form-select" required>
                                    <option value="">Sélectionner un projet...</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->title }} - {{ $project->student->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Date *</label>
                                        <input type="date" name="date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Heure *</label>
                                        <input type="time" name="time" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Lieu *</label>
                                        <input type="text" name="location" class="form-control"
                                            placeholder="Ex: Salle 101, Amphithéâtre A..." required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Durée (minutes)</label>
                                        <input type="number" name="duration" class="form-control" min="30"
                                            max="180" value="60">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Membres du jury *</label>
                                <div id="jury-members">
                                    <div class="jury-member mb-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" name="jury_members[0][name]" placeholder="Nom complet"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="email" name="jury_members[0][email]" placeholder="Email"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-jury" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-plus"></i> Ajouter un membre
                                </button>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.defenses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Programmer la soutenance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let juryCount = 1;

            document.getElementById('add-jury').addEventListener('click', function() {
                const juryContainer = document.getElementById('jury-members');
                const newJury = document.createElement('div');
                newJury.className = 'jury-member mb-2';
                newJury.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" name="jury_members[${juryCount}][name]" 
                           placeholder="Nom complet" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <input type="email" name="jury_members[${juryCount}][email]" 
                           placeholder="Email" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-jury">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
                juryContainer.appendChild(newJury);
                juryCount++;

                // Ajouter l'événement de suppression
                newJury.querySelector('.remove-jury').addEventListener('click', function() {
                    newJury.remove();
                });
            });
        });
    </script>
@endsection
