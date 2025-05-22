@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Documents des projets encadrés</div>

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
                                    <th>Étudiant</th>
                                    <th>Nom du document</th>
                                    <th>Type</th>
                                    <th>Date de téléversement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>
                                            <a href="{{ route('supervisor.projects.show', $document->project->id) }}">
                                                {{ $document->project->title }}
                                            </a>
                                        </td>
                                        <td>{{ $document->project->student->name }}</td>
                                        <td>{{ $document->original_name }}</td>
                                        <td>{{ $document->type }}</td>
                                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('supervisor.documents.download', [$document->project_id, $document->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun document disponible</td>
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