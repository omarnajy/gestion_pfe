@extends('layouts.admin')

@section('admin-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Gestion des Projets') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <div class="row mb-4">
                        <div class="col">
                            <form action="{{ route('admin.projects.index') }}" method="GET" class="d-flex gap-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Rechercher un projet..." name="search" value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                
                                 <select name="department" class="form-select" onchange="this.form.submit()">
                <option value="">Tous les départements</option>
                @php
                    // Récupérer tous les départements uniques
                    $departments = \App\Models\User::whereNotNull('department')
                        ->distinct('department')
                        ->pluck('department')
                        ->toArray();
                @endphp
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                        {{ $dept }}
                    </option>
                @endforeach
            </select>
                            </form>
                        </div>
                        <div class="col-auto">
        <a href="{{ route('admin.sync-projects-assignments') }}" class="btn btn-warning">
            <i class="fas fa-sync"></i> Synchroniser
        </a>
    </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Étudiant</th>
                                    <th>Encadreur</th>
                                    <th>Date de soumission</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                <tr>
                                    <td>{{ $project->title }}</td>
                                    <td>
                                @if($project->student)
                                    {{ $project->student->name }}
                                  <br>
                                  <small class="text-muted">{{ $project->student->email }}</small>
                                @else
                                  <span class="text-warning">Aucun étudiant assigné</span>
                                @endif
                                  </td>
                                    <td>
                                        @if($project->supervisor)
                                            {{ $project->supervisor->name }}
                                            <br>
                                            <small class="text-muted">{{ $project->supervisor->email }}</small>
                                        @else
                                            <span class="text-danger">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $project->status_color }}">
                                            {{ $project->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            
                                            @if($project->status === 'validated' && !$project->admin_validated)
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#validateModal{{ $project->id }}">
                                                <i class="fas fa-check-double"></i> Valider
                                            </button>
                                            @endif
                                            
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $projects->links() }}
                    </div>
                    
                    <!-- Modals for each project -->
                    @foreach($projects as $project)
                        <!-- Validate Modal -->
                        <div class="modal fade" id="validateModal{{ $project->id }}" tabindex="-1" aria-labelledby="validateModalLabel{{ $project->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.projects.validate', $project->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="validateModalLabel{{ $project->id }}">Validation finale du projet</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Êtes-vous sûr de vouloir valider définitivement le projet <strong>{{ $project->title }}</strong> ?</p>
                                            <p>Cette action marquera le projet comme finalisé dans le système.</p>
                                            <div class="mb-3">
                                                <label for="validation_comment" class="form-label">Commentaire (optionnel)</label>
                                                <textarea class="form-control" id="validation_comment" name="comment" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-success">Valider</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Assign Supervisor Modal -->
                        <div class="modal fade" id="assignSupervisorModal{{ $project->id }}" tabindex="-1" aria-labelledby="assignSupervisorModalLabel{{ $project->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.assignments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                
                @if($project->student)
                    <input type="hidden" name="student_id" value="{{ $project->student->id }}">
                
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignSupervisorModalLabel{{ $project->id }}">Assigner un encadreur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Assigner un encadreur au projet <strong>{{ $project->title }}</strong> de l'étudiant <strong>{{ $project->student->name }}</strong>.</p>
                        
                        <div class="mb-3">
                            <label for="supervisor_id" class="form-label">Sélectionner un encadreur</label>
                            <select class="form-select" id="supervisor_id" name="supervisor_id" required>
                                <option value="">-- Choisir un encadreur --</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">
                                        {{ $supervisor->name }} ({{ $supervisor->active_projects_count }} projets en cours)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Assigner</button>
                    </div>
                @else
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignSupervisorModalLabel{{ $project->id }}">Impossible d'assigner un encadreur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            Ce projet n'a pas d'étudiant assigné. Veuillez d'abord assigner un étudiant à ce projet avant d'attribuer un encadreur.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection