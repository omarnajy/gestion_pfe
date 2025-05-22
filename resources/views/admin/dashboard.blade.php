{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@php use App\Models\User; @endphp
@section('title', 'Tableau de bord Administrateur')

@section('page-title', 'Tableau de bord Administrateur')

@section('admin-content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Tableau de bord Administrateur</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <h2>{{ $userCount ?? 0 }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.users.index') }}">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Projets validés</h5>
                    <h2>{{ $approvedProjects ?? 0 }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.projects.index',['status' => 'approved']) }}">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Projets en attente</h5>
                    <h2>{{ $pendingProjects ?? 0 }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.projects.index',['status' => 'pending']) }}?status=pending">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Étudiants sans encadreur</h5>
                    <h2>{{ $studentsWithoutSupervisor ?? 0 }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.assignments') }}">Assigner</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Derniers projets soumis
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="recentProjectsTable">
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
                                                En attente
                                            @elseif($project->status == 'approved')
                                                Approuvé
                                            @elseif($project->status == 'rejected')
                                                Rejeté
                                            @else
                                                {{ $project->status }}
                                             @endif
                                        </td>
                                        <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a class="small text-white stretched-link" href="{{ route('admin.projects.show', $project->id) }}">Voir</a>
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

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Actions rapides</div>
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
                <div class="card-header">Notifications récentes</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($notifications ?? [] as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $notification->content }}
                            <span class="badge bg-primary rounded-pill">{{ $notification->created_at->diffForHumans() }}</span>
                        </li>
                        @empty
                        <li class="list-group-item">Aucune notification récente</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Exemple de script pour les graphiques (à adapter selon vos données réelles)
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des statuts de projet
        const statusCtx = document.getElementById('projectStatusChart');
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
            }
        });
        
        // Graphique des projets par encadreur
        const supervisorCtx = document.getElementById('projectsBySupervisorChart');
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
    });
</script>
@endpush
@endsection