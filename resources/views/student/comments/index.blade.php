@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Commentaires et remarques') }} - {{ $project->title }}</h5>
                    <a href="{{ route('student.projects.show', $project->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Retour au projet') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="timeline">
                        @forelse ($comments as $comment)
                            <div class="comment-item mb-4">
                                <div class="d-flex">
                                    <div class="comment-avatar mr-3">
                                        <div class="avatar-placeholder bg-{{ $comment->user->role === 'supervisor' ? 'primary' : 'secondary' }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="comment-content flex-grow-1">
                                        <div class="card">
                                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $comment->user->name }}</strong>
                                                    <span class="badge badge-{{ $comment->user->role === 'supervisor' ? 'primary' : 'secondary' }} ml-2">
                                                        {{ $comment->user->role === 'supervisor' ? 'Encadreur' : 'Étudiant' }}
                                                    </span>
                                                </div>
                                                <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <div class="card-body py-2 px-3">
                                                <p class="mb-0">{{ $comment->content }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($comment->attachment)
                                            <div class="attachment mt-2">
                                                <a href="{{ route('student.comments.attachment.download', $comment->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-paperclip"></i> {{ __('Télécharger la pièce jointe') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-comments fa-3x text-muted"></i>
                                </div>
                                <h5>{{ __('Aucun commentaire') }}</h5>
                                <p class="text-muted">{{ __('Aucun commentaire n\'a encore été ajouté à ce projet.') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <div class="add-comment">
                        <h5>{{ __('Ajouter un commentaire') }}</h5>
                        <form action="{{ route('student.comments.add', $project->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="3" placeholder="{{ __('Votre commentaire...') }}" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="attachment" class="form-label">{{ __('Pièce jointe (optionnel)') }}</label>
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                                <small class="form-text text-muted">{{ __('Formats acceptés: PDF, DOCX, PNG, JPG (max 5MB)') }}</small>
                                @error('attachment')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> {{ __('Envoyer') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection