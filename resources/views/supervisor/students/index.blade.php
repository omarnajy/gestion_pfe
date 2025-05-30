@extends('layouts.supervisor')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Étudiants encadrés') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Projet</th>
                                            <th>Statut du project</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student)
                                            <tr>
                                                <td>{{ $student ? $student->name : 'N/A' }}</td>
                                                <td>{{ $student ? $student->email : 'N/A' }}</td>
                                                <td>
                                                    @php
                                                        // Récupérer manuellement le projet de l'étudiant si la relation ne fonctionne pas
$project = \App\Models\Project::where(
    'student_id',
                                                            $student->id,
                                                        )->first();
                                                    @endphp

                                                    @if ($project)
                                                        {{ $project->title }}
                                                    @else
                                                        <span class="text-muted">Aucun projet</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($project)
                                                        @php
                                                            // Calculer dynamiquement le statut et la couleur
                                                            $statusColor = match ($project->status) {
                                                                'pending' => 'warning',
                                                                'approved' => 'success',
                                                                'rejected' => 'danger',
                                                                default => 'secondary',
                                                            };

                                                            $statusText = match ($project->status) {
                                                                'pending' => 'En attente',
                                                                'approved' => 'Approuvé',
                                                                'rejected' => 'Rejeté',
                                                                default => 'Inconnu',
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $statusColor }}">
                                                            {{ $statusText }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($student->project)
                                                        <a href="{{ route('supervisor.projects.show', $student->project->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> Voir le projet
                                                        </a>
                                                    @else
                                                        @php
                                                            // Essayer de trouver un projet associé directement via la base de données
                                                            $directProject = \App\Models\Project::where(
                                                                'student_id',
                                                                $student->id,
                                                            )->first();
                                                        @endphp

                                                        @if ($directProject)
                                                            <a href="{{ route('supervisor.projects.show', $directProject->id) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> Voir le projet
                                                            </a>
                                                        @else
                                                            <button class="btn btn-sm btn-secondary" disabled>
                                                                <i class="fas fa-eye"></i> Aucun projet
                                                            </button>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Vous n'encadrez actuellement aucun étudiant.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
