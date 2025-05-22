@extends('layouts.supervisor')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Commentaires sur le projet') }}: {{ $project->title }}</span>
                    <a href="{{ route('supervisor.projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Retour au projet') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <form method="POST" action="{{ route('supervisor.comments.store', $project) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label">{{ __('Ajouter un commentaire') }}</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="comment" name="content" rows="3" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> {{ __('Envoyer le commentaire') }}
                            </button>
                        </form>
                    </div>

                    <hr>

                    <h5 class="mt-4 mb-3">{{ __('Historique des commentaires') }}</h5>

                    @if($comments->isEmpty())
                        <div class="alert alert-info">
                            {{ __('Aucun commentaire n\'a encore été ajouté pour ce projet.') }}
                        </div>
                    @else
                        <div class="comment-list">
                            @foreach($comments as $comment)
                                <div class="comment-item card mb-3">
                                    <div class="card-header d-flex justify-content-between bg-light">
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong>
                                            <span class="badge bg-secondary ms-2">
                                                {{ $comment->user->role == 'student' ? __('Étudiant') : ($comment->user->role == 'supervisor' ? __('Encadreur') : __('Admin')) }}
                                            </span>
                                        </div>
                                        <div class="text-muted small">
                                            {{ $comment->created_at->format('d/m/Y à H:i') }}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $comments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection