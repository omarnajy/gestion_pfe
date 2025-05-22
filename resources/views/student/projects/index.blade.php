{{-- resources/views/student/projects/index.blade.php --}}
@extends('layouts.student')

@section('student-content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Mes Projets</h1>
        </div>
        <div class="col-md-4 text-end">
            @if(auth()->user()->projectsAsStudent()->where('status', '!=', 'rejected')->count() == 0)
                <div class="btn-group">
                    <a href="{{ route('student.projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Proposer un projet
                    </a>
                    <a href="{{ route('student.projects.available') }}" class="btn btn-success">
                        <i class="fas fa-list"></i> Voir les sujets disponibles
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @forelse($projects ?? [] as $project)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $project->title }}</h5>
                        <span class="badge 
                            @if($project->status == 'pending') bg-warning
                            @elseif($project->status == 'approved') bg-success
                            @elseif($project->status == 'rejected') bg-danger
                            @elseif($project->status == 'completed') bg-primary
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="card-subtitle mb-2 text-muted">Soumis le {{ $project->created_at->format('d/m/Y') }}</h6>
                                <p class="card-text">{{ Str::limit($project->description, 200) }}</p>
                                
                                @if($project->supervisor)
                                    <p><strong>Encadreur :</strong> {{ $project->supervisor->name }}</p>
                                @else
                                    <p><strong>Encadreur :</strong> <span class="text-muted">Non assigné</span></p>
                                @endif
                                
                                <a href="{{ route('student.projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Voir les détails
                                </a>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">Progrès du projet</div>
                                    <div class="card-body">
                                        @php
                                            // Calcul simplifié du progrès en fonction du statut
                                            $progress = match($project->status) {
                                                'pending' => 25,
                                                'approved' => 50,
                                                'in_progress' => 75,
                                                'completed' => 100,
                                                default => 0
                                            };
                                        @endphp
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ $progress }}% complété</small>
                                        
                                        <div class="mt-3">
                                            <small><strong>Dernière mise à jour :</strong> {{ $project->updated_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-comment"></i> {{ $project->comments_count ?? 0 }} commentaires
                                </small>
                                <small class="text-muted ms-3">
                                    <i class="fas fa-file"></i> {{ $project->documents_count ?? 0 }} documents
                                </small>
                            </div>
                            
                            @if($project->status == 'rejected')
                                <a href="{{ route('student.projects.create', ['from_project' => $project->id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-sync"></i> Soumettre à nouveau
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
            <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Aucun projet trouvé</h5>
                <p class="card-text">Vous n'avez pas encore de projet de fin d'études.</p>
                <div class="mt-4">
                    <p>Vous avez deux options pour démarrer :</p>
                    <div class="row justify-content-center mt-3">
                        <div class="col-md-5">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-lightbulb fa-3x text-warning mb-3"></i>
                                    <h5>Proposer votre sujet</h5>
                                    <p class="small">Soumettez votre propre idée de projet pour approbation</p>
                                    <a href="{{ route('student.projects.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Proposer un projet
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x text-success mb-3"></i>
                                    <h5>Choisir un sujet proposé</h5>
                                    <p class="small">Parcourez les sujets de PFE proposés par les encadreurs</p>
                                    <a href="{{ route('student.projects.available') }}" class="btn btn-success">
                                        <i class="fas fa-list"></i> Voir les sujets disponibles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection