@extends('layouts.admin')

@section('admin-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Gestion des documents') }}</h5>
                    <div>
                        <form action="{{ route('admin.documents') }}" method="GET" class="d-flex">
                            <div class="input-group me-2" style="width: 250px;">
                                <input type="text" class="form-control" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            
                            <select name="project_status" class="form-select me-2" onchange="this.form.submit()" style="width: 150px;">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('project_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ request('project_status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="rejected" {{ request('project_status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                                <option value="completed" {{ request('project_status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                            </select>
                            
                            <select name="file_type" class="form-select" onchange="this.form.submit()" style="width: 120px;">
                                <option value="">Tous types</option>
                                <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="docx" {{ request('file_type') == 'docx' ? 'selected' : '' }}>DOCX</option>
                                <option value="image" {{ request('file_type') == 'image' ? 'selected' : '' }}>Image</option>
                                <option value="other" {{ request('file_type') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Document') }}</th>
                                    <th>{{ __('Projet') }}</th>
                                    <th>{{ __('Étudiant') }}</th>
                                    <th>{{ __('Encadreur') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Taille') }}</th>
                                    <th>{{ __('Déposé le') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>{{ $document->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $document->project_id) }}" class="text-decoration-none">
                                                {{ $document->project->title }}
                                            </a>
                                            <span class="badge bg-{{ $document->project->status_color }}">{{ $document->project->status_label }}</span>
                                        </td>
                                        <td>{{ $document->project->student->name }}</td>
                                        <td>{{ $document->project->supervisor->name ?? 'Non assigné' }}</td>
                                        <td>
    @php
        $typeLabels = [
            'rapport' => 'Rapport',
            'presentation' => 'Présentation',
            'annexe' => 'Annexe',
            'autre' => 'Autre'
        ];
        
        $typeColors = [
            'rapport' => 'info',
            'presentation' => 'primary',
            'annexe' => 'success',
            'autre' => 'secondary'
        ];
        
        $typeLabel = $typeLabels[$document->type] ?? ucfirst($document->type);
        $typeColor = $typeColors[$document->type] ?? 'secondary';
    @endphp
    
    <span class="badge bg-{{ $typeColor }}">{{ $typeLabel }}</span>
</td>
                                        <td>
    @php
        $bytes = $document->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        $formatted_size = round($bytes, 2) . ' ' . $units[$i];
    @endphp
    {{ $formatted_size }}
</td>
                                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.documents.download', $document->id) }}" class="btn btn-sm btn-outline-primary" title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#documentInfoModal{{ $document->id }}" title="Détails">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal{{ $document->id }}" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Modal pour les détails du document -->
                                            <div class="modal fade" id="documentInfoModal{{ $document->id }}" tabindex="-1" aria-labelledby="documentInfoModalLabel{{ $document->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="documentInfoModalLabel{{ $document->id }}">{{ $document->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <th>Projet:</th>
                                                                    <td>{{ $document->project->title }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Description:</th>
                                                                    <td>{{ $document->description ?? 'Aucune description' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Type:</th>
                                                                    <td>{{ strtoupper($document->file_extension) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Taille:</th>
                                                                    <td>{{ $document->file_size_formatted }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Créé le:</th>
                                                                    <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Modifié le:</th>
                                                                    <td>{{ $document->updated_at->format('d/m/Y H:i') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Téléchargé:</th>
                                                                    <td>{{ $document->download_count }} fois</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                            <a href="{{ route('admin.documents.download', $document->id) }}" class="btn btn-primary">
                                                                <i class="fas fa-download"></i> Télécharger
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Modal pour la suppression -->
                                            <div class="modal fade" id="deleteDocumentModal{{ $document->id }}" tabindex="-1" aria-labelledby="deleteDocumentModalLabel{{ $document->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteDocumentModalLabel{{ $document->id }}">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer le document <strong>{{ $document->name }}</strong> ?
                                                            <div class="alert alert-warning mt-3">
                                                                <i class="fas fa-exclamation-triangle"></i> Cette action est irréversible.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('admin.documents.destroy', $document->id) }}" method="POST"class="d-inline">
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
                                            <h5>{{ __('Aucun document trouvé') }}</h5>
                                            <p class="text-muted">{{ __('Aucun document ne correspond à vos critères de recherche.') }}</p>
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