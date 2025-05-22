@extends('layouts.supervisor')

@section('title', 'Étudiants')

@section('content')
<div class="container-fluid">
    @if(request('student'))
        <!-- Vue détaillée d'un étudiant -->
        <div class="row">
            <div class="col-md-4">
                <!-- Profil de l'étudiant -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" 
                                 src="{{ $student->profile_image ?? asset('img/default-avatar.png') }}" 
                                 alt="Photo de profil">
                        </div>
                        
                        <h3 class="profile-username text-center">{{ $student->name }}</h3>
                        <p class="text-muted text-center">Étudiant</p>
                        
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Email</b> <a class="float-right">{{ $student->email }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Téléphone</b> <a class="float-right">{{ $student->phone ?? 'Non renseigné' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Projets</b> <a class="float-right">{{ $student->projects->count() }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Date d'inscription</b> <a class="float-right">{{ $student->created_at->format('d/m/Y') }}</a>
                            </li>
                        </ul>
                        
                        <a href="{{ route('supervisor.students') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                
                <!-- À propos de l'étudiant -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">À propos</h3>
                    </div>
                    <div class="card-body">
                        @if($student->bio)
                            <p>{!! nl2br(e($student->bio)) !!}</p>
                        @else
                            <p class="text-muted">Aucune information biographique disponible.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Statistiques de l'étudiant -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Statistiques</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $student->completedProjects()->count() }}</h5>
                                    <span class="description-text">PROJETS TERMINÉS</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $student->activeProjects()->count() }}</h5>
                                    <span class="description-text">PROJETS EN COURS</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($student->projects->isNotEmpty())
                            <div class="mt-4">
                                <p><strong>Note moyenne :</strong> {{ number_format($student->averageGrade(), 2) }}/20</p>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($student->averageGrade() / 20) * 100 }}%" aria-valuenow="{{ $student->averageGrade() }}" aria-valuemin="0" aria-valuemax="20"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Projets de l'étudiant -->
                <div class="card">
                    <div class="card-header p-2">
                        <h3 class="card-title">Projets de l'étudiant</h3>
                    </div>
                    <div class="card-body">
                        @if($student->projects->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Date de début</th>
                                            <th>Date de fin</th>
                                            <th>Statut</th>
                                            <th>Progression</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->projects as $project)
                                            <tr>
                                                <td>{{ $project->title }}</td>
                                                <td>{{ $project->start_date->format('d/m/Y') }}</td>
                                                <td>
                                                    {{ $project->end_date->format('d/m/Y') }}
                                                    @if($project->end_date->diffInDays(now()) <= 7 && $project->end_date->isFuture() && $project->status != 'completed')
                                                        <span class="badge bg-danger">Échéance proche</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($project->status == 'pending')
                                                        <span class="badge bg-warning">En attente</span>
                                                    @elseif($project->status == 'in_progress')
                                                        <span class="badge bg-info">En cours</span>
                                                    @elseif($project->status == 'completed')
                                                        <span class="badge bg-success">Terminé</span>
                                                    @elseif($project->status == 'rejected')
                                                        <span class="badge bg-danger">Rejeté</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar bg-success" style="width: {{ $project->progress }}%"></div>
                                                    </div>
                                                    <span class="badge bg-secondary">{{ $project->progress }}%</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('supervisor.projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Cet étudiant n'a pas encore de projet.
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Activités récentes de l'étudiant -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activités récentes</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline timeline-inverse">
                            @if($activities->count() > 0)
                                @foreach($activities as $date => $dateActivities)
                                    <div class="time-label">
                                        <span class="bg-primary">{{ $date }}</span>
                                    </div>
                                    
                                    @foreach($dateActivities as $activity)
                                        <div>
                                            @if($activity->type == 'task_submission')
                                                <i class="fas fa-file-upload bg-info"></i>
                                            @elseif($activity->type == 'comment')
                                                <i class="fas fa-comment bg-success"></i>
                                            @elseif($activity->type == 'project_creation')
                                                <i class="fas fa-project-diagram bg-warning"></i>
                                            @else
                                                <i class="fas fa-circle bg-secondary"></i>
                                            @endif
                                            
                                            <div class="timeline-item">
                                                <span class="time">
                                                    <i class="fas fa-clock"></i> {{ $activity->created_at->format('H:i') }}
                                                </span>
                                                <h3 class="timeline-header">
                                                    @if($activity->type == 'task_submission')
                                                        <strong>Soumission de livrable</strong> pour le projet <a href="{{ route('supervisor.projects.show', $activity->project->id) }}">{{ $activity->project->title }}</a>
                                                    @elseif($activity->type == 'comment')
                                                        <strong>Commentaire</strong> sur le projet <a href="{{ route('supervisor.projects.show', $activity->project->id) }}">{{ $activity->project->title }}</a>
                                                    @elseif($activity->type == 'project_creation')
                                                        <strong>Création du projet</strong> <a href="{{ route('supervisor.projects.show', $activity->project->id) }}">{{ $activity->project->title }}</a>
                                                    @endif
                                                </h3>
                                                
                                                <div class="timeline-body">
                                                    @if($activity->description)
                                                        {{ $activity->description }}
                                                    @endif
                                                </div>
                                                
                                                @if($activity->type == 'task_submission' && $activity->task)
                                                    <div class="timeline-footer">
                                                        <a href="{{ route('supervisor.projects.evaluate', $activity->project->id) }}?task={{ $activity->task->id }}" class="btn btn-warning btn-sm">
                                                            Évaluer
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            @else
                                <div class="timeline-item">
                                    <div class="timeline-body">
                                        Aucune activité récente pour cet étudiant.
                                    </div>
                                </div>
                            @endif
                            
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Liste des étudiants -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Liste des étudiants encadrés</h3>
                        <div class="card-tools">
                            <form action="{{ route('supervisor.students') }}" method="GET" class="form-inline">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="search" class="form-control float-right" placeholder="Rechercher" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Projets</th>
                                        <th>Projets actifs</th>
                                        <th>Note moyenne</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->phone ?? 'Non renseigné' }}</td>
                                            <td>{{ $student->projects->count() }}</td>
                                            <td>{{ $student->activeProjects()->count() }}</td>
                                            <td>
                                                @if($student->projects->isNotEmpty())
                                                    {{ number_format($student->averageGrade(), 2) }}/20
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('supervisor.students') }}?student={{ $student->id }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Aucun étudiant trouvé</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $students->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques globales -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Statistiques des étudiants</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total d'étudiants</span>
                                        <span class="info-box-number">{{ $totalStudents }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-project-diagram"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Projets actifs</span>
                                        <span class="info-box-number">{{ $activeProjects }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-tasks"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Livrables en attente</span>
                                        <span class="info-box-number">{{ $pendingTasks }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Échéances proches</span>
                                        <span class="info-box-number">{{ $upcomingDeadlines }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Graphique de répartition des notes -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Répartition des notes</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="gradesChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Statut des projets</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="projectStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(!request('student'))
        // Configuration du graphique des notes
        const gradesCtx = document.getElementById('gradesChart').getContext('2d');
        const gradesChart = new Chart(gradesCtx, {
            type: 'bar',
            data: {
                labels: ['0-5', '6-10', '11-14', '15-17', '18-20'],
                datasets: [{
                    label: 'Répartition des notes',
                    data: [
                        {{ $gradesDistribution['0-5'] ?? 0 }},
                        {{ $gradesDistribution['6-10'] ?? 0 }},
                        {{ $gradesDistribution['11-14'] ?? 0 }},
                        {{ $gradesDistribution['15-17'] ?? 0 }},
                        {{ $gradesDistribution['18-20'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
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
        
        // Configuration du graphique des statuts de projets
        const projectStatusCtx = document.getElementById('projectStatusChart').getContext('2d');
        const projectStatusChart = new Chart(projectStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'En cours', 'Terminés', 'Rejetés'],
                datasets: [{
                    data: [
                        {{ $projectStatusCount['pending'] ?? 0 }},
                        {{ $projectStatusCount['in_progress'] ?? 0 }},
                        {{ $projectStatusCount['completed'] ?? 0 }},
                        {{ $projectStatusCount['rejected'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    @endif
</script>
@endsection