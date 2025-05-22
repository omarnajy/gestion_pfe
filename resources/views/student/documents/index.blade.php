@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Mes documents</span>
                    <a href="{{ route('student.projects') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux projets
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-{{ session('alert-type', 'success') }}" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Projet</th>
                                    <th>Nom du document</th>
                                    <th>Type</th>
                                    <th>Taille</th>
                                    <th>Date de téléversement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>
                                            <a href="{{ route('student.projects.show', $document->project->id) }}">
                                                {{ $document->project->title }}
                                            </a>
                                        </td>
                                        <td>{{ $document->original_name }}</td>
                                        <td>{{ $document->type }}</td>
                                        <td>{{ number_format($document->size / 1024, 2) }} KB</td>
                                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('student.documents.download', [$document->project_id, $document->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Vous n'avez pas encore téléversé de documents</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection