@extends('layouts.student')

@section('student-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Détails du Projet') }}</span>
                    <a href="{{ route('student.projects.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux projets
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col">
                            <h3>{{ $project->title }}</h3>
                            
                            <div class="badge bg-{{ $project->status_color }} mb-3">
                                {{ $project->status_text }}
                            </div>
                            
                            <p class="text-muted">
                                <strong>Encadreur:</strong> 
                                {{ $project->supervisor ? $project->supervisor->name : 'Non assigné' }}
                            </p>
                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    Description du projet
                                </div>
                                <div class="card-body">
                                    <p>{{ $project->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="mb-4">
                        <h4>Timeline</h4>
                        <div class="timeline">
                            @foreach($project->timeline ?? [] as $event)
                            <div class="timeline-item">
                                <div class="timeline-badge bg-{{ $event->type_color }}">
                                    <i class="fas fa-{{ $event->icon }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>{{ $event->title }}</h6>
                                    <p class="text-muted small">{{ $event->created_at->format('d/m/Y H:i') }}</p>
                                    <p>{{ $event->description }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="row mb-4">
                        <div class="col">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Documents</span>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                        <i class="fas fa-upload"></i> Ajouter un document
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if(($project->documents ?? collect())->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Type</th>
                                                        <th>Taille</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($project->documents as $document)
                                                    <tr>
                                                        <td>{{ $document->name }}</td>
                                                        <td>{{ $document->type }}</td>
                                                        <td>{{ $document->size_formatted }}</td>
                                                        <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('student.documents.download', ['project' => $project->id, 'document' => $document->id]) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            Aucun document n'a été ajouté à ce projet.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commentaires -->
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    Commentaires et Remarques
                                </div>
                                <div class="card-body">
                                    @if($project->comments->count() > 0)
                                        <div class="comments-list">
                                            @foreach($project->comments as $comment)
                                            <div class="comment-item mb-3">
                                                <div class="comment-header d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $comment->user->name }}</strong> 
                                                        <span class="badge bg-secondary">{{ $comment->user->role_text }}</span>
                                                    </div>
                                                    <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                                <div class="comment-body p-3 bg-light rounded">
                                                    {{ $comment->content }}
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            Aucun commentaire pour le moment.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour l'upload de document -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('student.documents.store', ['project' => $project->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Ajouter un document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_name" class="form-label">Nom du document</label>
                        <input type="text" class="form-control" id="document_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Type de document</label>
                        <select class="form-control" id="document_type" name="type" required>
                            <option value="rapport">Rapport</option>
                            <option value="presentation">Présentation</option>
                            <option value="cahier_charges">Cahier des charges</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="document_file" class="form-label">Fichier</label>
                        <input type="file" class="form-control" id="document_file" name="file" required>
                        <small class="form-text text-muted">Formats acceptés: PDF, DOCX, PPT (max 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Téléverser</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        height: 100%;
        width: 2px;
        background: #e9ecef;
        left: 20px;
        top: 0;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 60px;
        margin-bottom: 20px;
    }
    
    .timeline-badge {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        position: absolute;
        left: 0;
        color: white;
        text-align: center;
        line-height: 40px;
    }
    
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
    
    .comment-item {
        border-left: 3px solid #6c757d;
        padding-left: 15px;
    }
</style>
@endsection