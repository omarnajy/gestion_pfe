@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Chronologie du projet : {{ $project->title }}</h4>
                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour au projet
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-{{ session('alert-type', 'success') }}" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="timeline">
                        @forelse($project->timelineEvents as $event)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas {{ $event->icon_class }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4 class="timeline-title">
                                        {{ $event->title }}
                                        <small class="text-muted">{{ $event->created_at->format('d/m/Y H:i') }}</small>
                                    </h4>
                                    <p>{{ $event->description }}</p>
                                    @if($event->user)
                                        <small>Par : {{ $event->user->name }}</small>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-4">
                                <p class="text-muted">Aucun événement n'a été enregistré pour ce projet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #ddd;
        left: 31px;
        margin-left: -1.5px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    
    .timeline-marker {
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        left: 31px;
        margin-left: -8px;
        border: 2px solid #fff;
        background: #f8f9fa;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .timeline-content {
        margin-left: 80px;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
    
    .timeline-title {
        margin-top: 0;
        font-size: 16px;
        font-weight: 600;
    }
</style>
@endsection