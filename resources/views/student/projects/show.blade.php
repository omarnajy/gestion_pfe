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
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

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

                        <!-- Documents -->
                        <div class="row mb-4">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span>Documents</span>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#uploadDocumentModal">
                                            <i class="fas fa-upload"></i> Ajouter un document
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        @if (($project->documents ?? collect())->count() > 0)
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
                                                        @foreach ($project->documents as $document)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                                                        <strong>{{ $document->name ?: 'Document sans nom' }}</strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $typeLabels = [
                                                                            'rapport' => 'Rapport',
                                                                            'presentation' => 'Présentation',
                                                                            'cahier_charges' => 'Cahier des charges',
                                                                            'autre' => 'Autre',
                                                                        ];
                                                                        $typeColors = [
                                                                            'rapport' => 'info',
                                                                            'presentation' => 'primary',
                                                                            'cahier_charges' => 'warning',
                                                                            'autre' => 'secondary',
                                                                        ];
                                                                        $typeLabel =
                                                                            $typeLabels[$document->type] ??
                                                                            ucfirst($document->type);
                                                                        $typeColor =
                                                                            $typeColors[$document->type] ?? 'secondary';
                                                                    @endphp
                                                                    <span
                                                                        class="badge bg-{{ $typeColor }}">{{ $typeLabel }}</span>
                                                                </td>
                                                                <td>{{ $document->formatted_size ?? '0 B' }}</td>
                                                                <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                                <td>
                                                                    <a href="{{ route('student.documents.download', ['project' => $project->id, 'document' => $document->id]) }}"
                                                                        class="btn btn-sm btn-info" title="Télécharger">
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
                                                <i class="fas fa-info-circle"></i>
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
                                        @if ($project->comments->count() > 0)
                                            <div class="comments-list">
                                                @foreach ($project->comments as $comment)
                                                    <div class="comment-item mb-3">
                                                        <div
                                                            class="comment-header d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $comment->user->name }}</strong>
                                                                <span
                                                                    class="badge bg-secondary">{{ $comment->user->role_text }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <small
                                                                    class="text-muted me-2">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                                                @if ($comment->user_id == auth()->id())
                                                                    <form
                                                                        action="{{ route('student.comments.destroy', $comment->id) }}"
                                                                        method="POST" style="display: inline;"
                                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-danger"
                                                                            title="Supprimer le commentaire">
                                                                            <i class="fas fa-trash fa-xs"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="comment-body p-3 bg-light rounded">
                                                            {{ $comment->content }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
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
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('student.documents.store', ['project' => $project->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadDocumentModalLabel">Ajouter un document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="document_name" class="form-label">Nom du document <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="document_name" name="name" placeholder="Entrez le nom du document"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Le nom que vous souhaitez donner à ce document</small>
                        </div>
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Type de document <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="document_type"
                                name="type" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="rapport" {{ old('type') == 'rapport' ? 'selected' : '' }}>Rapport</option>
                                <option value="presentation" {{ old('type') == 'presentation' ? 'selected' : '' }}>
                                    Présentation</option>
                                <option value="cahier_charges" {{ old('type') == 'cahier_charges' ? 'selected' : '' }}>
                                    Cahier des charges</option>
                                <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="document_file" class="form-label">Fichier <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                id="document_file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Formats acceptés: PDF, DOC, DOCX, PPT, PPTX (max
                                10MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Téléverser
                        </button>
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
