@extends('layouts.supervisor')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Détails du Projet') }}</span>
                        <a href="{{ route('supervisor.projects.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux projets
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h3>{{ $project->title }}</h3>

                                <div class="badge bg-{{ $project->status_color }} mb-3">
                                    {{ $project->status_text }}
                                </div>

                                <p class="text-muted">
                                    <strong>Étudiant:</strong>
                                    {{ $project->student->name }} ({{ $project->student->email }})
                                </p>

                                <div class="card mb-4">
                                    <div class="card-header">
                                        Description du projet
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $project->description }}</p>
                                    </div>
                                </div>

                                @if ($project->status === 'pending')
                                    <div class="d-flex gap-2 mb-4">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#validateProjectModal">
                                            <i class="fas fa-check"></i> Valider le projet
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectProjectModal">
                                            <i class="fas fa-times"></i> Rejeter le projet
                                        </button>
                                    </div>
                                @endif

                                @if ($project->status === 'validated' && !$project->has_been_rated)
                                    <div class="mb-4">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#rateProjectModal">
                                            <i class="fas fa-star"></i> Noter le projet
                                        </button>
                                    </div>
                                @endif

                                @if ($project->has_been_rated)
                                    <div class="card mb-4">
                                        <div class="card-header bg-warning text-white">
                                            Évaluation du projet
                                        </div>
                                        <div class="card-body">
                                            <h5>Note: {{ $project->rating }}/20</h5>
                                            <p><strong>Commentaire:</strong> {{ $project->rating_comment }}</p>
                                            <p class="text-muted small">Évalué le {{ $project->rated_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        Informations
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <i class="fas fa-calendar"></i> Soumis le:
                                                {{ $project->created_at->format('d/m/Y') }}
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fas fa-file-alt"></i> Documents:
                                                {{ $project->documents->count() }}
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fas fa-comments"></i> Commentaires:
                                                {{ $project->comments->count() }}
                                            </li>
                                            @if ($project->status === 'validated' || $project->status === 'approved')
                                                <li class="list-group-item">
                                                    <i class="fas fa-check-circle text-success"></i> Validé le:
                                                    {{ $project->updated_at->format('d/m/Y') }}
                                                </li>
                                            @elseif($project->status === 'rejected')
                                                <li class="list-group-item">
                                                    <i class="fas fa-times-circle text-danger"></i> Rejeté le:
                                                    {{ $project->updated_at->format('d/m/Y') }}
                                                </li>
                                            @endif

                                            <li class="list-group-item">
                                                <i class="fas fa-edit"></i> Dernière modification:
                                                {{ $project->updated_at->format('d/m/Y H:i') }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="row mb-4">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">
                                        <span>Documents</span>
                                    </div>
                                    <div class="card-body">
                                        @if ($project->documents->count() > 0)
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
                                                                <td>{{ $document->name }}</td>
                                                                <td>{{ $document->type }}</td>
                                                                <td>{{ $document->size_formatted }}</td>
                                                                <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                                                <td>
                                                                    <a href="{{ route('supervisor.documents.download', ['project' => $project->id, 'document' => $document->id]) }}"
                                                                        class="btn btn-sm btn-info">
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

                        <!-- Commentaires et remarques -->
                        <div class="row mb-4">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">
                                        <span>Commentaires et remarques</span>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('supervisor.comments.store', $project->id) }}"
                                            method="POST" class="mb-4">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="comment_content" class="form-label">Ajouter une remarque</label>
                                                <textarea class="form-control" id="comment_content" name="content" rows="3" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Envoyer
                                            </button>
                                        </form>

                                        <hr>

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
                                                                        action="{{ route('supervisor.comments.destroy', $comment->id) }}"
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

    <!-- Modal de validation -->
    <div class="modal fade" id="validateProjectModal" tabindex="-1" aria-labelledby="validateProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('supervisor.projects.validate', $project->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="validateProjectModalLabel">Valider le projet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir valider le projet <strong>{{ $project->title }}</strong> ?</p>
                        <div class="mb-3">
                            <label for="validation_comment" class="form-label">Commentaire (optionnel)</label>
                            <textarea class="form-control" id="validation_comment" name="comment" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de rejet - Version simplifiée sans colonnes supplémentaires -->
    <div class="modal fade" id="rejectProjectModal" tabindex="-1" aria-labelledby="rejectProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('supervisor.projects.reject', $project->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectProjectModalLabel">Rejeter le projet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Vous êtes sur le point de rejeter le projet <strong>"{{ $project->title }}"</strong>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">
                                Raison du rejet <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="reason" name="reason" rows="5" required
                                placeholder="Expliquez clairement pourquoi vous rejetez ce projet. Cette explication aidera l'étudiant à comprendre et améliorer son travail."></textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Votre commentaire sera ajouté au projet et visible par l'étudiant.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban me-1"></i>Rejeter le projet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'évaluation -->
    <div class="modal fade" id="rateProjectModal" tabindex="-1" aria-labelledby="rateProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('supervisor.projects.evaluate', $project->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="rateProjectModalLabel">Noter le projet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Évaluation du projet <strong>{{ $project->title }}</strong></p>

                        <div class="mb-3">
                            <label for="rating" class="form-label">Note (sur 20)</label>
                            <input type="number" class="form-control" id="rating" name="rating" min="0"
                                max="20" step="0.5" required>
                        </div>

                        <div class="mb-3">
                            <label for="rating_comment" class="form-label">Commentaire d'évaluation</label>
                            <textarea class="form-control" id="rating_comment" name="comment" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">Soumettre la note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .comment-item {
            border-left: 3px solid #6c757d;
            padding-left: 15px;
        }
    </style>
@endsection
