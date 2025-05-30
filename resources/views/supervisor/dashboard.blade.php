@extends('layouts.supervisor')

@section('title', 'Tableau de bord Encadreur')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Tableau de bord Encadreur</h2>
            <p class="text-muted">Gérez les projets de fin d'études que vous encadrez</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('supervisor.students.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-users me-1"></i>Mes étudiants
            </a>
            <a href="{{ route('supervisor.projects.index') }}" class="btn btn-primary">
                <i class="fas fa-tasks me-1"></i>Tous les projets
            </a>
        </div>
    </div>
    {{-- Notifications de soutenance pour le superviseur --}}
    @if (auth()->user()->notifications()->where('type', 'defense_scheduled')->where('read', false)->exists())
        <div class="row mb-4">
            <div class="col-12">
                @foreach (auth()->user()->notifications()->where('type', 'defense_scheduled')->where('read', false)->get() as $notification)
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-gavel fa-2x me-3 text-info"></i>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading mb-1">
                                    <i class="fas fa-user-graduate me-1"></i>{{ $notification->title }}
                                </h5>
                                <p class="mb-2">{{ $notification->message }}</p>

                                @if ($notification->data)
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <div class="card bg-light border-0">
                                                <div class="card-body py-2">

                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="card-title mb-1">Détails de la soutenance </h6><br>
                                                        <span><i class="fas fa-calendar text-primary"></i>
                                                            {{ \Carbon\Carbon::parse($notification->data['date'])->format('d/m/Y') }}</span>
                                                        <span><i class="fas fa-clock text-info"></i>
                                                            {{ \Carbon\Carbon::parse($notification->data['time'])->format('H:i') }}</span>
                                                        <span><i class="fas fa-map-marker-alt text-success"></i>
                                                            {{ $notification->data['location'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-7">
            <!-- Projets en attente de validation -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-hourglass-half me-2"></i>Projets en attente de validation</h5>
                    <span class="badge bg-dark">{{ $pendingProjectsCount ?? 0 }}</span>
                </div>
                <div class="card-body">
                    @if (isset($pendingProjectsCollection) && $pendingProjectsCollection->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Étudiant</th>
                                        <th>Soumis le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingProjectsCollection ?? [] as $project)
                                        <tr>
                                            <td>
                                                <a
                                                    href="{{ route('supervisor.projects.show', $project->id) }}">{{ $project->title }}</a>
                                            </td>
                                            <td>
                                                @if ($project->student_id)
                                                    {{ optional($project->student)->name ?? 'Non assigné' }}
                                                @else
                                                    Non assigné
                                                @endif
                                            </td>
                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('supervisor.projects.show', $project->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="lead">Aucun projet en attente de validation</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mes statistiques -->
            <div class="card shadow mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Mes statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h2 class="mb-0">{{ $totalProjects }}</h2>
                                <small class="text-muted">Total projets</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h2 class="mb-0">{{ $approvedProjects ?? 0 }}</h2>
                                <small class="text-muted">Approuvés</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h2 class="mb-0">{{ $rejectedProjects }}</h2>
                                <small class="text-muted">Rejetés</small>
                            </div>
                        </div>
                    </div>

                    <div>
                        <canvas id="projectsChart" width="100%" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <!-- Commentaires récents  -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Commentaires récents</h5>
                </div>
                <div class="card-body">
                    @if (!empty($recentComments) && count($recentComments) > 0)
                        @foreach ($recentComments as $comment)
                            <div class="comment-card mb-4 border rounded p-3 bg-light">
                                <div class="d-flex align-items-start">
                                    <div class="avatar bg-secondary rounded-circle text-white p-2 me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 fw-bold">{{ $comment->user->name }}</h6>
                                            <span class="badge bg-info">{{ $comment->created_at->format('d/m/Y') }}</span>
                                        </div>
                                        <p class="mb-2 small text-muted">
                                            <i class="fas fa-file-alt me-1"></i> Projet:
                                            <strong>{{ $comment->project->title }}</strong>
                                        </p>
                                        <div class="comment-content border-start border-primary ps-2 mt-2">
                                            <p class="mb-0">{{ Str::limit($comment->content, 150) }}</p>
                                        </div>
                                        <div class="text-end mt-2">
                                            <a href="{{ route('supervisor.projects.show', $comment->project_id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir le projet
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($recentComments) > 3)
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-list me-1"></i>Voir tous les commentaires
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-4x text-muted mb-3 opacity-25"></i>
                            <p class="lead">Aucun commentaire récent</p>
                            <p class="text-muted">Les commentaires et remarques sur vos projets apparaîtront ici.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .comment-card {
            transition: all 0.2s ease-in-out;
        }

        .comment-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .comment-content {
            border-width: 3px !important;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('projectsChart').getContext('2d');
            var projectsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'Approuvés', 'Rejetés'],
                    datasets: [{
                        data: [
                            {{ $pendingProjectsCount ?? 0 }},
                            {{ $approvedProjects ?? 0 }},
                            {{ $rejectedProjects ?? 0 }}
                        ],
                        backgroundColor: [
                            '#ffc107', // Jaune pour en attente
                            '#28a745', // Vert pour approuvés
                            '#dc3545' // Rouge pour rejetés
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
