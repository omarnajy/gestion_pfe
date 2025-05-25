@extends('layouts.student')

@section('title', 'Tableau de bord Étudiant')

@section('page-title', 'Tableau de bord Étudiant')

@section('student-content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h1 class="h3">Tableau de bord Étudiant</h1>
            </div>
            <div class="col-md-4 text-right">
                @if (!$hasProject)
                    <a href="{{ route('student.projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Proposer un sujet de PFE
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Colonne principale -->
            <div class="col-md-8">
                @if ($hasProject)
                    <!-- Informations du projet -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Mon projet de fin d'études</h3>
                            <div class="card-tools">
                                <a href="{{ route('student.projects.show', $project->id) }}" class="btn btn-tool">
                                    <i class="fas fa-eye"></i> Voir détails
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4>{{ $project->title }}</h4>

                            <div class="mb-3 d-flex align-items-center">
                                <span
                                    class="badge badge-{{ $project->status_color }} mr-2">{{ $project->status_label }}</span>
                                @if ($project->supervisor)
                                    <span>Encadré par: <strong>{{ $project->supervisor->name }}</strong></span>
                                @else
                                    <span class="text-warning">En attente d'affectation d'un encadreur</span>
                                @endif
                            </div>

                            <div class="callout callout-info">
                                <p>{{ Str::limit($project->description, 200) }}</p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-clock"></i> Dernière mise à jour:
                                    {{ $project->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="card card-primary card-outline mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Documents</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="modal"
                                    data-target="#uploadDocumentModal">
                                    <i class="fas fa-upload"></i> Ajouter
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
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
                                        @forelse($documents ?? [] as $document)
                                            <tr>
                                                <td>{{ $document->name }}</td>
                                                <td><span
                                                        class="badge badge-secondary">{{ strtoupper($document->type) }}</span>
                                                </td>
                                                <td>{{ $document->size_formatted ?? number_format($document->size / 1024, 2) . ' KB' }}
                                                </td>
                                                <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('student.documents.download', [$project->id, $document->id]) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <!-- Formulaire de suppression -->
                                                        <form
                                                            action="{{ route('student.documents.destroy', [$project->id, $document->id]) }}"
                                                            method="POST" style="display: inline;"
                                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer le document {{ $document->name }} ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Aucun document disponible</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal d'upload de document -->
                    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog"
                        aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadDocumentModalLabel">Téléverser un document</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('student.documents.store', $project->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="documentName">Nom du document</label>
                                            <input type="text" class="form-control" id="documentName" name="name"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="documentType">Type de document</label>
                                            <select class="form-control" id="documentType" name="type" required>
                                                <option value="rapport">Rapport</option>
                                                <option value="presentation">Présentation</option>
                                                <option value="annexe">Annexe</option>
                                                <option value="autre">Autre</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="documentFile">Fichier</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="documentFile"
                                                    name="document" required accept=".pdf,.doc,.docx,.ppt,.pptx">
                                                <label class="custom-file-label" for="documentFile">Choisir un
                                                    fichier</label>
                                            </div>
                                            <small class="form-text text-muted">Formats acceptés: PDF, DOC, DOCX, PPT,
                                                PPTX. Taille max: 10MB</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Téléverser</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Pas de projet -->
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                                <h4>Vous n'avez pas encore de projet de fin d'études</h4>
                                <p class="text-muted">Commencez par proposer un sujet de PFE pour débuter votre projet</p>
                                <a href="{{ route('student.projects.create') }}" class="btn btn-lg btn-primary mt-3">
                                    <i class="fas fa-plus-circle mr-2"></i> Proposer un sujet
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Colonne latérale -->
            <div class="col-md-4">
                <!-- Remarques et commentaires -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Remarques de l'encadreur</h3>
                    </div>
                    <div class="card-body p-0">
                        @if ($hasProject && isset($comments) && $comments->count() > 0)
                            <div class="direct-chat-messages" style="height: 400px; overflow-y: scroll; padding: 10px;">
                                @foreach ($comments as $comment)
                                    <div
                                        class="direct-chat-msg {{ $comment->user_id != auth()->id() ? '' : 'right' }} mb-3">
                                        <div class="direct-chat-infos clearfix">
                                            <span
                                                class="direct-chat-name {{ $comment->user_id != auth()->id() ? 'float-left' : 'float-right' }}">
                                                {{ $comment->user->name }}
                                            </span>
                                            <span
                                                class="direct-chat-timestamp {{ $comment->user_id != auth()->id() ? 'float-right' : 'float-left' }}">
                                                {{ $comment->created_at->format('d M H:i') }}
                                                @if ($comment->user_id == auth()->id())
                                                    <form action="{{ route('student.comments.destroy', $comment->id) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-link text-danger p-0 ml-2"
                                                            title="Supprimer">
                                                            <i class="fas fa-trash fa-xs"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </span>
                                        </div>
                                        <img class="direct-chat-img"
                                            src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=random"
                                            alt="User Image">
                                        <div class="direct-chat-text">
                                            {{ $comment->content }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($hasProject)
                                <div class="card-footer">
                                    <form action="{{ route('student.comments.store', $project->id) }}" method="post">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="content" placeholder="Répondre ..."
                                                class="form-control" required>
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-primary">Envoyer</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @elseif($hasProject)
                            <div class="card-body">
                                <p class="text-center py-3">Aucune remarque pour le moment</p>
                            </div>
                        @else
                            <div class="card-body">
                                <p class="text-center py-3">Les remarques apparaîtront ici une fois que vous aurez proposé
                                    un sujet de PFE</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Script pour afficher le nom du fichier sélectionné -->
    <script>
        $(document).ready(function() {
            $('input[type="file"]').change(function(e) {
                var fileName = e.target.files[0].name;
                $(this).next('.custom-file-label').html(fileName);
            });
        });

        // Si des messages de succès ou d'erreur existent, les afficher avec toastr
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    </script>
@endsection
