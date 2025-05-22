@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Téléverser un document') }}</span>
                    <a href="{{ route('student.projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Retour au projet') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('student.documents.store', $project) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nom du document') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Type de document') }}</label>
                            <div class="col-md-6">
                                <select id="type" class="form-control @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">{{ __('Sélectionner un type') }}</option>
                                    <option value="CAHIER_CHARGES" {{ old('type') == 'CAHIER_CHARGES' ? 'selected' : '' }}>{{ __('Cahier des charges') }}</option>
                                    <option value="RAPPORT_INTERMEDIAIRE" {{ old('type') == 'RAPPORT_INTERMEDIAIRE' ? 'selected' : '' }}>{{ __('Rapport intermédiaire') }}</option>
                                    <option value="RAPPORT_FINAL" {{ old('type') == 'RAPPORT_FINAL' ? 'selected' : '' }}>{{ __('Rapport final') }}</option>
                                    <option value="PRESENTATION" {{ old('type') == 'PRESENTATION' ? 'selected' : '' }}>{{ __('Présentation') }}</option>
                                    <option value="ANNEXE" {{ old('type') == 'ANNEXE' ? 'selected' : '' }}>{{ __('Annexe / Document complémentaire') }}</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="file" class="col-md-4 col-form-label text-md-right">{{ __('Fichier') }}</label>
                            <div class="col-md-6">
                                <div class="custom-file">
                                    <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                                    @error('file')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        {{ __('Formats acceptés : PDF, DOCX, DOC, PPTX, PPT (Max. 20MB)') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> {{ __('Téléverser') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection