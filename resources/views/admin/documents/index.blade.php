@extends('layouts.admin')

@section('admin-content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Gestion des documents</h5>

                        <!-- Barre de recherche et filtrage -->
                        <form action="{{ route('admin.documents') }}" method="GET" class="d-flex">
                            @if (request('project_id'))
                                <input type="hidden" name="project_id" value="{{ request('project_id') }}">
                            @endif

                            <div class="input-group me-2" style="width: 250px;">
                                <input type="text" class="form-control" name="search" placeholder="Rechercher..."
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                            <select name="file_type" class="form-select" onchange="this.form.submit()"
                                style="width: 150px;">
                                <option value="">Tous types</option>
                                <option value="rapport" {{ request('file_type') == 'rapport' ? 'selected' : '' }}>Rapport
                                </option>
                                <option value="presentation" {{ request('file_type') == 'presentation' ? 'selected' : '' }}>
                                    Présentation</option>
                                <option value="annexe" {{ request('file_type') == 'annexe' ? 'selected' : '' }}>Annexe
                                </option>
                                <option value="cahier_charges"
                                    {{ request('file_type') == 'cahier_charges' ? 'selected' : '' }}>Cahier des charges
                                </option>
                                <option value="autre" {{ request('file_type') == 'autre' ? 'selected' : '' }}>Autre
                                </option>
                            </select>
                        </form>
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

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th>Projet</th>
                                        <th>Étudiant</th>
                                        <th>Encadreur</th>
                                        <th>Type</th>
                                        <th>Taille</th>
                                        <th>Déposé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents as $document)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-alt me-2 text-primary"></i>
                                                    <div>
                                                        <strong>{{ $document->name ?: 'Document sans nom' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $document->mime_type }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.projects.show', $document->project_id) }}"
                                                    class="text-decoration-none">
                                                    {{ $document->project->title }}
                                                </a>
                                            </td>
                                            <td>{{ $document->project->student->name ?? 'Non assigné' }}</td>
                                            <td>{{ $document->project->supervisor->name ?? 'Non assigné' }}</td>
                                            <td>
                                                @php
                                                    $typeLabels = [
                                                        'rapport' => 'Rapport',
                                                        'presentation' => 'Présentation',
                                                        'annexe' => 'Annexe',
                                                        'cahier_charges' => 'Cahier des charges',
                                                        'autre' => 'Autre',
                                                    ];

                                                    $typeColors = [
                                                        'rapport' => 'info',
                                                        'presentation' => 'primary',
                                                        'annexe' => 'success',
                                                        'cahier_charges' => 'warning',
                                                        'autre' => 'secondary',
                                                    ];

                                                    $typeLabel =
                                                        $typeLabels[$document->type] ?? ucfirst($document->type);
                                                    $typeColor = $typeColors[$document->type] ?? 'secondary';
                                                @endphp

                                                <span class="badge bg-{{ $typeColor }}">{{ $typeLabel }}</span>
                                            </td>
                                            <td>{{ $document->formatted_size }}</td>
                                            <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.documents.download', $document->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteDocumentModal{{ $document->id }}"
                                                        title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal pour la suppression -->
                                                <div class="modal fade" id="deleteDocumentModal{{ $document->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="deleteDocumentModalLabel{{ $document->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteDocumentModalLabel{{ $document->id }}">
                                                                    Confirmer la suppression
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Êtes-vous sûr de vouloir supprimer le document
                                                                <strong>{{ $document->name ?: 'ce document' }}</strong> ?
                                                                <div class="alert alert-warning mt-3">
                                                                    <i class="fas fa-exclamation-triangle"></i> Cette action
                                                                    est irréversible.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Annuler</button>
                                                                <form
                                                                    action="{{ route('admin.documents.destroy', $document->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="fas fa-trash"></i> Supprimer
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="fas fa-file-alt fa-3x text-muted"></i>
                                                </div>
                                                <h5>Aucun document trouvé</h5>
                                                <p class="text-muted">Aucun document ne correspond à vos critères de
                                                    recherche.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $documents->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
