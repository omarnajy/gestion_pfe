@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ $project->title }}</h4>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informations générales</h5>
                            <div class="mb-2">
                                <strong>Statut:</strong> 
                                <span class="badge 
                                    @if($project->status == 'En attente') badge-warning 
                                    @elseif($project->status == 'Validé') badge-success 
                                    @elseif($project->status == 'Rejeté') badge-danger 
                                    @else badge-info @endif">
                                    {{ $project->status }}
                                </span>
                            </div>
                            <div class="mb-2"><strong>Étudiant:</strong> {{ $project->student->name }}</div>
                            <div class="mb-2"><strong>Encadreur:</strong> {{ $project->supervisor->name ?? 'Non assigné' }}</div>
                            <div class="mb-2"><strong>Date de création:</strong> {{ $project->created_at->format('d/m/Y') }}</div>
                            <div class="mb-2"><strong>Dernière mise à jour:</strong> {{ $project->updated_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <h5>Actions</h5>
                            <div class="btn-group mb-3">
                                <a href="{{ route('projects.timeline', $project->id) }}" class="btn btn-info">
                                    <i class="fas fa-history"></i> Chronologie
                                </a>
                                
                                @if(auth()->user()->role == 'student' && auth()->id() == $project->student_id)
                                    <a href="{{ route('student.comments', $project->id) }}" class="btn btn-primary">
                                        <i class="fas fa-comments"></i> Commentaires
                                    </a>
                                @endif
                                
                                @if(auth()->user()->role == 'supervisor' && auth()->id() == $project->supervisor_id)
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCommentModal">
                                        <i class="fas fa-comment-medical"></i> Ajouter un commentaire
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>Description du projet</h5>
                            <div class="p-3 bg-light rounded">
                                {{ $project->description }}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Documents</h5>
                            @if($project->documents->count() > 0)
                                <table class="table table-striped">
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
                                            <td>{{ $document->original_name }}</td>
                                            <td>{{ $document->type }}</td>
                                            <td>{{ number_format($document->size / 1024, 2) }} KB</td>
                                            <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if(auth()->user()->role == 'student')
                                                    <a href="{{ route('student.documents.download', [$project->id, $document->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @elseif(auth()->user()->role == 'supervisor')
                                                    <a href="{{ route('supervisor.documents.download', [$project->id, $document->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @elseif(auth()->user()->role == 'admin')
                                                    <a href="{{ route('admin.documents.download', [$document->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-info">
                                    Aucun document n'a été téléversé pour ce projet.
                                </div>
                            @endif
                            
                            @if(auth()->user()->role == 'student' && auth()->id() == $project->student_id)
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadDocumentModal">
                                    <i class="fas fa-file-upload"></i> Téléverser un document
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour téléverser un document -->
@if(auth()->user()->role == 'student' && auth()->id() == $project->student_id)
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">Téléverser un document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('student.documents.store', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="document">Sélectionnez un fichier</label>
                        <input type="file" class="form-control-file" id="document" name="document" required>
                        <small class="form-text text-muted">Formats acceptés: PDF, DOCX (max: 10MB)</small>
                    </div>
                    <div class="form-group">
                        <label for="type">Type de document</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="Rapport">Rapport</option>
                            <option value="Présentation">Présentation</option>
                            <option value="Cahier des charges">Cahier des charges</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Téléverser</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal pour ajouter un commentaire (encadreur) -->
@if(auth()->user()->role == 'supervisor' && auth()->id() == $project->supervisor_id)
<div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCommentModalLabel">Ajouter un commentaire</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('supervisor.comments.add', $project->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="content">Commentaire</label>
                        <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection