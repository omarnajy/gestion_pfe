{{-- resources/views/admin/assignments.blade.php --}}
@extends('layouts.admin')

@section('admin-content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestion des Affectations</h1>
            <p class="text-muted">Gérez les affectations d'encadreurs aux étudiants et leurs projets</p>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAssignmentModal">
                <i class="fas fa-plus me-1"></i> Nouvelle affectation
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="assignmentsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab" aria-controls="current" aria-selected="true">Affectations en cours</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="unassigned-tab" data-bs-toggle="tab" data-bs-target="#unassigned" type="button" role="tab" aria-controls="unassigned" aria-selected="false">Étudiants non affectés</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="supervisors-tab" data-bs-toggle="tab" data-bs-target="#supervisors" type="button" role="tab" aria-controls="supervisors" aria-selected="false">Encadreurs</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="assignmentsTabsContent">
                        <div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Étudiant</th>
                                            <th>Filière</th>
                                            <th>Encadreur</th>
                                            <th>Spécialité</th>
                                            <th>Date d'affectation</th>
                                            <th>Projet</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($currentAssignments ?? [] as $assignment)
                                        <tr>
                                            <td>             
                                                {{ $assignment->student->name }}
                                            </td>
                                            <td>{{ $assignment->student->field ?? 'Non défini' }}</td>
                                            <td>{{ $assignment->supervisor->name }}</td>
                                            <td>{{ $assignment->supervisor->specialty ?? 'Non spécifiée' }}</td>
                                            <td>{{ $assignment->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    // Récupérer le projet de l'étudiant (s'il en a un)
                                                    $project = \App\Models\Project::where('student_id', $assignment->student_id)->first();
                                                @endphp
                                                
                                                @if($project)
                                                    <a href="{{ route('admin.projects.show', $project->id) }}" class="text-decoration-none">
                                                        <span class="badge bg-success">{{ $project->title }}</span>
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">Aucun projet</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAssignmentModal{{ $assignment->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAssignmentModal{{ $assignment->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Modals pour l'édition et la suppression -->
                                                @include('admin.partials.edit-assignment-modal', ['assignment' => $assignment])
                                                @include('admin.partials.delete-assignment-modal', ['assignment' => $assignment])
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">
                                                <div class="py-4">
                                                    <i class="fas fa-users-slash fa-3x mb-3"></i>
                                                    <p>Aucune affectation trouvée</p>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newAssignmentModal">
                                                        <i class="fas fa-plus me-1"></i> Créer une affectation
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="unassigned" role="tabpanel" aria-labelledby="unassigned-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Filière</th>
                                            <th>Projet</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($unassignedStudents ?? [] as $student)
                                        @php
                                            // Récupérer le projet de l'étudiant s'il en a un
                                            $studentProject = \App\Models\Project::where('student_id', $student->id)->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $student->name }}
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->field ?? 'Non définie' }}</td>
                                            <td>
                                                @if($studentProject)
                                                    <a href="{{ route('admin.projects.show', $studentProject->id) }}" class="text-decoration-none">
                                                        <span class="badge bg-success">{{ $studentProject->title }}</span>
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">Aucun projet</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignStudentModal{{ $student->id }}">
                                                    <i class="fas fa-user-plus me-1"></i> Affecter
                                                </button>
                                                
                                                <!-- Modal pour l'affectation -->
                                                @include('admin.partials.assign-student-modal', ['student' => $student])
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-muted">
                                                <div class="py-4">
                                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                    <p>Tous les étudiants sont affectés</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="supervisors" role="tabpanel" aria-labelledby="supervisors-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nom</th>
                                            <th>Spécialité</th>
                                            <th>Département</th>
                                            <th>Étudiants encadrés</th>
                                            <th>Maximum</th>
                                            <th>Taux d'occupation</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($supervisors ?? [] as $supervisor)
                                        @php
                                            // Compter le nombre d'étudiants encadrés
                                            $assignedStudentsCount = $currentAssignments->where('supervisor_id', $supervisor->id)->count();
                                            $maxStudents = $supervisor->max_students ?? 5;
                                            $occupancyRate = ($maxStudents > 0) ? ($assignedStudentsCount / $maxStudents) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $supervisor->name }}
                                            </td>
                                            <td>{{ $supervisor->specialty ?? 'Non spécifiée' }}</td>
                                            <td>{{ $supervisor->department ?? 'Non spécifié' }}</td>
                                            <td>{{ $assignedStudentsCount }}</td>
                                            <td>{{ $maxStudents }}</td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar 
                                                        @if($occupancyRate >= 90) bg-danger
                                                        @elseif($occupancyRate >= 70) bg-warning
                                                        @else bg-success
                                                        @endif" 
                                                        role="progressbar" 
                                                        style="width: {{ $occupancyRate }}%;" 
                                                        aria-valuenow="{{ $occupancyRate }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small>{{ round($occupancyRate) }}%</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assignments') }}?supervisor_id={{ $supervisor->id }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye me-1"></i> Voir les étudiants
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">
                                                <div class="py-4">
                                                    <i class="fas fa-user-tie fa-3x mb-3 text-secondary"></i>
                                                    <p>Aucun encadreur trouvé</p>
                                                    <a href="{{ route('admin.users.create', 'supervisor') }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-plus me-1"></i> Ajouter un encadreur
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour nouvelle affectation -->
<div class="modal fade" id="newAssignmentModal" tabindex="-1" aria-labelledby="newAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAssignmentModalLabel">Nouvelle affectation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.assignments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Étudiant</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">Sélectionner un étudiant</option>
                            @foreach($unassignedStudents ?? [] as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->field ?? 'Filière non définie' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="supervisor_id" class="form-label">Encadreur</label>
                        <select class="form-select" id="supervisor_id" name="supervisor_id" required>
                            <option value="">Sélectionner un encadreur</option>
                            @foreach($availableSupervisors ?? [] as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }} ({{ $supervisor->specialty ?? 'Spécialité non définie' }}) - {{ $supervisor->students_count ?? 0 }}/{{ $supervisor->max_students ?? 5 }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Si l'étudiant a déjà un projet, celui-ci sera automatiquement assigné à l'encadreur sélectionné.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script pour réinitialiser les modals lors de la fermeture
    document.addEventListener('DOMContentLoaded', function() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                const forms = this.querySelectorAll('form');
                forms.forEach(form => form.reset());
            });
        });
    });
</script>
@endpush
@endsection