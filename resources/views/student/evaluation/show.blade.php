{{-- resources/views/student/evaluation/show.blade.php --}}
@extends('layouts.student')

@section('title', 'Mon Évaluation')
@section('page-title', 'Évaluation de mon projet')

@section('student-content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Évaluation du projet : {{ $project->title }}</h5>
                    <span class="badge bg-info">{{ $project->status_text }}</span>
                </div>
                <div class="card-body">
                    @if ($evaluation)
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h2 class="text-primary">{{ number_format($evaluation->grade, 2) }}/20</h2>
                                        <p class="mb-0">Note Générale</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-5 text-center">
                                        <strong>{{ $evaluation->presentation_grade }}/20</strong>
                                        <p class="small text-muted">Présentation</p>
                                    </div>
                                    <div class="col-5 text-center">
                                        <strong>{{ $evaluation->documentation_grade }}/20</strong>
                                        <p class="small text-muted">Documentation</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>Évalué par : {{ $evaluation->evaluator->name }}</span>
                                    <span>Le : {{ $evaluation->created_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>Aucune évaluation disponible</h5>
                            <p>Votre projet n'a pas encore été évalué par votre encadreur.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Informations du projet</div>
                <div class="card-body">
                    <p><strong>Encadreur :</strong> {{ $project->supervisor->name ?? 'Non assigné' }}</p>
                    <p><strong>Date de soumission :</strong> {{ $project->created_at->format('d/m/Y') }}</p>
                    <p><strong>Statut :</strong> <span
                            class="badge bg-{{ $project->status_color }}">{{ $project->status_text }}</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection
