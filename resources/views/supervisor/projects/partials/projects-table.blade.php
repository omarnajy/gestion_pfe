@if ($projects->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Étudiant</th>
                    <th>Date de soumission</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td>{{ $project->title }}</td>
                        <td>
                            @if ($project->student)
                                {{ $project->student->name }}
                                <br>
                                <small class="text-muted">{{ $project->student->email }}</small>
                            @else
                                <span class="text-muted">Pas encore assigné</span>
                                @if ($project->is_proposed_by_supervisor)
                                    <br>
                                    <small class="badge bg-info">Projet proposé</small>
                                @endif
                            @endif
                        </td>
                        <td>{{ $project->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $project->status_color }}">
                                {{ $project->status_text }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('supervisor.projects.show', $project->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>

                                @if ($project->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#validateModal{{ $project->id }}">
                                        <i class="fas fa-check"></i> Valider
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal{{ $project->id }}">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                @endif

                                @if ($project->status === 'validated' && !$project->has_been_rated)
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#rateModal{{ $project->id }}">
                                        <i class="fas fa-star"></i> Noter
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modals for each project -->
    @foreach ($projects as $project)
        <!-- Validate Modal -->
        <div class="modal fade" id="validateModal{{ $project->id }}" tabindex="-1"
            aria-labelledby="validateModalLabel{{ $project->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('supervisor.projects.validate', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="validateModalLabel{{ $project->id }}">Valider le projet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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

        <!-- Correction du modal de rejet dans projects-table.blade.php -->

        <!-- Reject Modal - Version simplifiée -->
        <div class="modal fade" id="rejectModal{{ $project->id }}" tabindex="-1"
            aria-labelledby="rejectModalLabel{{ $project->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('supervisor.projects.reject', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel{{ $project->id }}">Rejeter le projet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir rejeter le projet <strong>{{ $project->title }}</strong> ?</p>
                            <div class="mb-3">
                                <label for="reason{{ $project->id }}" class="form-label">Raison du rejet <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason{{ $project->id }}" name="reason" rows="4" required
                                    placeholder="Expliquez pourquoi vous rejetez ce projet..."></textarea>
                                <small class="form-text text-muted">
                                    Cette explication sera ajoutée comme commentaire et visible par l'étudiant.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Rejeter le projet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Rate Modal -->
        <div class="modal fade" id="rateModal{{ $project->id }}" tabindex="-1"
            aria-labelledby="rateModalLabel{{ $project->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('supervisor.projects.evaluate', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="rateModalLabel{{ $project->id }}">Noter le projet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Évaluation du projet <strong>{{ $project->title }}</strong></p>

                            <div class="mb-3">
                                <label for="rating" class="form-label">Note (sur 20)</label>
                                <input type="number" class="form-control" id="rating" name="rating"
                                    min="0" max="20" step="0.5" required>
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
    @endforeach
@else
    <div class="alert alert-info">
        Aucun projet à afficher.
    </div>
@endif
