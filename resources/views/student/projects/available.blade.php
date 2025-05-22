{{-- resources/views/student/projects/available.blade.php --}}
@extends('layouts.student')

@section('title', 'Sujets disponibles')

@section('student-content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Sujets de PFE disponibles</h1>
            <p class="text-muted">Parcourez les sujets proposés par les encadreurs et choisissez celui qui vous intéresse.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('student.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à mes projets
            </a>
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
            @forelse($projects as $project)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $project->title }}</h5>
                        <span class="badge bg-info">Proposé par encadreur</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="card-subtitle mb-2 text-muted">Proposé le {{ $project->created_at->format('d/m/Y') }}</h6>
                                <p class="card-text">{{ Str::limit($project->description, 200) }}</p>
                                
                                @if($project->supervisor)
                                    <p><strong>Encadreur :</strong> {{ $project->supervisor->name }}</p>
                                @endif
                                
                                @if($project->keywords)
                                    <p><strong>Mots-clés :</strong> {{ $project->keywords }}</p>
                                @endif
                                
                                @if($project->technologies)
                                    <p><strong>Technologies :</strong> {{ $project->technologies }}</p>
                                @endif
                                
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal{{ $project->id }}">
                                    <i class="fas fa-info-circle"></i> Détails et candidature
                                </button>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">Informations</div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-user-tie me-2"></i> <strong>Encadreur :</strong> {{ $project->supervisor->name }}</li>
                                            <li><i class="fas fa-graduation-cap me-2"></i> <strong>Département :</strong> {{ $project->supervisor->department ?? 'Non spécifié' }}</li>
                                            <li><i class="fas fa-calendar-alt me-2"></i> <strong>Date de proposition :</strong> {{ $project->created_at->format('d/m/Y') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal pour les détails du projet et la candidature -->
                <div class="modal fade" id="projectModal{{ $project->id }}" tabindex="-1" aria-labelledby="projectModalLabel{{ $project->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="projectModalLabel{{ $project->id }}">{{ $project->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-4">
                                    <h6 class="text-primary">Description du projet</h6>
                                    <p>{{ $project->description }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="text-primary">Informations sur l'encadreur</h6>
                                    <p><strong>Nom :</strong> {{ $project->supervisor->name }}</p>
                                    <p><strong>Département :</strong> {{ $project->supervisor->department ?? 'Non spécifié' }}</p>
                                    <p><strong>Spécialité :</strong> {{ $project->supervisor->specialty ?? 'Non spécifiée' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="text-primary">Détails techniques</h6>
                                    <p><strong>Technologies :</strong> {{ $project->technologies ?? 'Non spécifiées' }}</p>
                                    <p><strong>Mots-clés :</strong> {{ $project->keywords ?? 'Non spécifiés' }}</p>
                                </div>
                                
                                <div>
                                    <h6 class="text-primary">Soumettre votre candidature</h6>
                                    <form action="{{ route('student.projects.apply', $project->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="motivation" class="form-label">Lettre de motivation</label>
                                            <textarea class="form-control" id="motivation" name="motivation" rows="4" required placeholder="Expliquez pourquoi vous êtes intéressé par ce projet et pourquoi vous seriez un bon candidat..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Soumettre ma candidature
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Aucun sujet disponible</h5>
                        <p class="card-text">Il n'y a actuellement aucun sujet de PFE proposé par les encadreurs.</p>
                        <a href="{{ route('student.projects.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> Proposer mon propre sujet
                        </a>
                    </div>
                </div>
            @endforelse
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>
@endsection