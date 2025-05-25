{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@php use App\Models\User; @endphp
@section('title', 'Tableau de bord Administrateur')

@section('page-title', 'Tableau de bord Administrateur')

@section('admin-content')
<div class="row">
    <div class="col-12">
        <h1>Tableau de bord Administrateur</h1>
        
        <!-- Cartes statistiques avec style AdminLTE -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $userCount ?? 0 }}</h3>
                        <p>Utilisateurs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                        Voir détails <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $approvedProjects ?? 0 }}</h3>
                        <p>Projets validés</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('admin.projects.index',['status' => 'approved']) }}" class="small-box-footer">
                        Voir détails <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingProjects ?? 0 }}</h3>
                        <p>Projets en attente</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('admin.projects.index',['status' => 'pending']) }}?status=pending" class="small-box-footer">
                        Voir détails <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $studentsWithoutSupervisor ?? 0 }}</h3>
                        <p>Étudiants sans encadreur</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <a href="{{ route('admin.assignments') }}" class="small-box-footer">
                        Assigner <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tableau des derniers projets soumis -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table mr-1"></i>
                            Derniers projets soumis
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="recentProjectsTable">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Étudiant</th>
                                        <th>Encadreur</th>
                                        <th>Statut</th>
                                        <th>Date de soumission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projects as $project)
                                        <tr>
                                            <td>{{ $project->title }}</td>
                                            <td>{{ $project->student->name ?? 'Non assigné' }}</td>
                                            <td>
                                                @if($project->supervisor)
                                                    {{ $project->supervisor->name }}
                                                @elseif($project->student && $project->student->assignments && $project->student->assignments->first())
                                                    {{ User::find($project->student->assignments->first()->supervisor_id)->name ?? 'Non assigné' }}
                                                @else
                                                    Non assigné
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->status == 'pending')
                                                    <span class="badge badge-warning">En attente</span>
                                                @elseif($project->status == 'approved')
                                                    <span class="badge badge-success">Approuvé</span>
                                                @elseif($project->status == 'rejected')
                                                    <span class="badge badge-danger">Rejeté</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $project->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucun projet récent</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides et notifications -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>
                            Actions rapides
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="{{ route('admin.users.create', 'student') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus me-2"></i> Créer un compte étudiant
                            </a>
                            <a href="{{ route('admin.users.create', 'supervisor') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-tie me-2"></i> Créer un compte encadreur
                            </a>
                            <a href="{{ route('admin.assignments') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-link me-2"></i> Gérer les affectations
                            </a>
                            <a href="{{ route('admin.statistics') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-line me-2"></i> Afficher les statistiques détaillées
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bell mr-1"></i>
                            Notifications récentes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($notifications ?? [] as $notification)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $notification->title ?? 'Notification' }}</h6>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notification->content ?? $notification->message }}</p>
                            </div>
                            @empty
                            <div class="list-group-item">
                                <p class="text-muted text-center mb-0">Aucune notification récente</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Exemple de script pour les graphiques (à adapter selon vos données réelles)
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier si les éléments canvas existent avant de créer les graphiques
        const statusCtx = document.getElementById('projectStatusChart');
        if (statusCtx) {
            const statusChart = new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: ['En attente', 'Approuvés', 'Rejetés', 'Terminés'],
                    datasets: [{
                        data: [
                            {{ $statusCounts['pending'] ?? 0 }}, 
                            {{ $statusCounts['approved'] ?? 0 }}, 
                            {{ $statusCounts['rejected'] ?? 0 }}, 
                            {{ $statusCounts['completed'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#ffc107',
                            '#28a745',
                            '#dc3545',
                            '#0d6efd'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Graphique des projets par encadreur
        const supervisorCtx = document.getElementById('projectsBySupervisorChart');
        if (supervisorCtx) {
            const supervisorChart = new Chart(supervisorCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($supervisorNames ?? []) !!},
                    datasets: [{
                        label: 'Nombre de projets',
                        data: {!! json_encode($supervisorCounts ?? []) !!},
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush