@extends('layouts.supervisor')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>{{ __('Projets à encadrer') }}</div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#proposeProjectModal">
                        <i class="fas fa-plus-circle me-1"></i> Proposer sujet
                    </button>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                                Tous <span class="badge bg-secondary rounded-pill">{{ $projects->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                                En attente <span class="badge bg-warning rounded-pill">{{ $pendingProjects->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="validated-tab" data-bs-toggle="tab" data-bs-target="#validated" type="button" role="tab" aria-controls="validated" aria-selected="false">
                                Validés <span class="badge bg-success rounded-pill">{{ $validatedProjects->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                                Rejetés <span class="badge bg-danger rounded-pill">{{ $rejectedProjects->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="projectTabsContent">
                        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                            @include('supervisor.projects.partials.projects-table', ['projects' => $projects])
                        </div>
                        <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            @include('supervisor.projects.partials.projects-table', ['projects' => $pendingProjects])
                        </div>
                        <div class="tab-pane fade" id="validated" role="tabpanel" aria-labelledby="validated-tab">
                            @include('supervisor.projects.partials.projects-table', ['projects' => $validatedProjects])
                        </div>
                        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                            @include('supervisor.projects.partials.projects-table', ['projects' => $rejectedProjects])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Proposer Sujet -->
<div class="modal fade" id="proposeProjectModal" tabindex="-1" aria-labelledby="proposeProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('supervisor.projects.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="proposeProjectModalLabel">Proposer un nouveau sujet de PFE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du projet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                        <div class="form-text">Décrivez le projet, ses objectifs et les résultats attendus.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="domain" class="form-label">Domaine <span class="text-danger">*</span></label>
                            <select class="form-select" id="domain" name="domain" required>
                                <option value="">Sélectionner un domaine</option>
                                <option value="web_development">Développement Web</option>
                                <option value="mobile_development">Développement Mobile</option>
                                <option value="data_science">Science des Données</option>
                                <option value="artificial_intelligence">Intelligence Artificielle</option>
                                <option value="cybersecurity">Cybersécurité</option>
                                <option value="networking">Réseaux</option>
                                <option value="embedded_systems">Systèmes Embarqués</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="technology" class="form-label">Technologies principales</label>
                            <input type="text" class="form-control" id="technology" name="technology" placeholder="Ex: Laravel, React, TensorFlow...">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_students" class="form-label">Nombre d'étudiants</label>
                            <select class="form-select" id="max_students" name="max_students">
                                <option value="1">1 étudiant</option>
                                <option value="2" selected>2 étudiants</option>
                                <option value="3">3 étudiants</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="difficulty" class="form-label">Niveau de difficulté</label>
                            <select class="form-select" id="difficulty" name="difficulty">
                                <option value="easy">Facile</option>
                                <option value="medium" selected>Moyen</option>
                                <option value="hard">Difficile</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="prerequisites" class="form-label">Prérequis</label>
                        <textarea class="form-control" id="prerequisites" name="prerequisites" rows="3"></textarea>
                        <div class="form-text">Compétences ou connaissances nécessaires pour mener à bien ce projet.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="expected_results" class="form-label">Résultats attendus</label>
                        <textarea class="form-control" id="expected_results" name="expected_results" rows="3"></textarea>
                    </div>

                    <input type="hidden" name="supervisor_id" value="{{ Auth::id() }}">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Proposer le sujet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialiser les éléments du formulaire si nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter une validation côté client pour le formulaire
        const form = document.querySelector('#proposeProjectModal form');
        form.addEventListener('submit', function(e) {
            // Validation basique
            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;
            const domain = document.getElementById('domain').value;
            
            if(!title.trim() || !description.trim() || !domain) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }
            
            // Si tout est valide, on ferme le modal et on ajoute un message d'attente
            const modal = bootstrap.Modal.getInstance(document.getElementById('proposeProjectModal'));
            modal.hide();
            
            // Afficher un message d'attente en haut du contenu
            const cardBody = document.querySelector('.card-body');
            const waitingAlert = document.createElement('div');
            waitingAlert.className = 'alert alert-info';
            waitingAlert.textContent = 'Traitement en cours...';
            cardBody.insertBefore(waitingAlert, cardBody.firstChild);
        });
        
        // Si un paramètre de statut est présent dans l'URL, activer l'onglet correspondant
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        if (status) {
            // Activer l'onglet correspondant
            const tab = document.querySelector(`#${status}-tab`);
            if (tab) {
                bootstrap.Tab.getOrCreateInstance(tab).show();
            }
        }
    });
</script>
@endsection