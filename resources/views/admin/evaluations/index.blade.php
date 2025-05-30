{{-- resources/views/admin/evaluations/index.blade.php --}}
@extends('layouts.admin')

@section('admin-content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Gestion des Évaluations</h1>
                <p class="text-muted">Vue d'ensemble de toutes les évaluations des projets PFE</p>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                <p class="mb-0">Total évaluations</p>
                            </div>
                            <div><i class="fas fa-star fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ number_format($stats['average_grade'], 2) }}/20</h4>
                                <p class="mb-0">Note moyenne</p>
                            </div>
                            <div><i class="fas fa-chart-line fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ number_format($stats['success_rate'], 2) }}%</h4>
                                <p class="mb-0">Taux de réussite</p>
                            </div>
                            <div><i class="fas fa-trophy fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filtres de recherche</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.evaluations.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="supervisor" placeholder="Nom de l'encadreur"
                                value="{{ request('supervisor') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="student" placeholder="Nom de l'étudiant"
                                value="{{ request('student') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="grade_min" placeholder="Note min"
                                min="0" max="20" step="0.1" value="{{ request('grade_min') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="grade_max" placeholder="Note max"
                                min="0" max="20" step="0.1" value="{{ request('grade_max') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des évaluations -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Liste des évaluations ({{ $evaluations->total() }} résultats)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th>Étudiant</th>
                                <th>Encadreur</th>
                                <th>Note finale</th>
                                <th>Détail des notes</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluations as $evaluation)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.projects.show', $evaluation->project_id) }}"
                                            class="text-decoration-none">
                                            {{ Str::limit($evaluation->project->title, 40) }}
                                        </a>
                                    </td>
                                    <td>{{ $evaluation->project->student->name }}</td>
                                    <td>{{ $evaluation->evaluator->name }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $evaluation->grade >= 16 ? 'success' : ($evaluation->grade >= 12 ? 'warning' : ($evaluation->grade >= 10 ? 'info' : 'danger')) }} fs-6">
                                            {{ number_format($evaluation->grade, 2) }}/20
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            Prés: {{ $evaluation->presentation_grade }}/20<br>
                                            Doc: {{ $evaluation->documentation_grade }}/20
                                        </small>
                                    </td>
                                    <td>{{ $evaluation->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#evaluationModal{{ $evaluation->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                        <p>Aucune évaluation trouvée</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $evaluations->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modals pour voir les détails -->
    @foreach ($evaluations as $evaluation)
        <div class="modal fade" id="evaluationModal{{ $evaluation->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détail de l'évaluation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Projet :</strong> {{ $evaluation->project->title }}<br>
                                <strong>Étudiant :</strong> {{ $evaluation->project->student->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Encadreur :</strong> {{ $evaluation->evaluator->name }}<br>
                                <strong>Date :</strong> {{ $evaluation->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>

                        <div class="row mb-3">

                            <div class="col-md-3 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h4>{{ $evaluation->presentation_grade }}/20</h4>
                                        <small>Présentation</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h4>{{ $evaluation->documentation_grade }}/20</h4>
                                        <small>Documentation</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h4>{{ number_format($evaluation->grade, 2) }}/20</h4>
                                        <small>MOYENNE</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
