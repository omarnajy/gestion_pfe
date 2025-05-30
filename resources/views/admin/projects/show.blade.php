@extends('layouts.admin')

@section('admin-content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0 fw-bold">{{ __('Détails du projet') }}</h4>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Retour à la liste') }}
                        </a>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="project-details">
                                    <h3 class="fw-bold text-dark mb-3">{{ $project->title }}</h3>

                                    <div class="d-flex gap-2 mb-3">
                                        @php
                                            $statusText = '';
                                            $statusClass = '';

                                            switch ($project->status) {
                                                case 'pending':
                                                    $statusText = 'En attente';
                                                    $statusClass = 'bg-warning';
                                                    break;
                                                case 'approved':
                                                    $statusText = 'Approuvé';
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'rejected':
                                                    $statusText = 'Rejeté';
                                                    $statusClass = 'bg-danger';
                                                    break;
                                                default:
                                                    $statusText = $project->status;
                                                    $statusClass = 'bg-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }} text-white fs-6">{{ $statusText }}</span>
                                        @if ($project->field)
                                            <span class="badge bg-secondary">{{ $project->field }}</span>
                                        @endif
                                    </div>

                                    <div class="mb-4 bg-light p-3 rounded">
                                        <h5 class="border-bottom pb-2 text-primary">{{ __('Description') }}</h5>
                                        <p class="mb-0">{{ $project->description ?: 'Aucune description fournie.' }}</p>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0 text-primary">{{ __('Informations') }}</h5>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-hover mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <th style="width:35%" class="ps-3">{{ __('Étudiant') }}
                                                                </th>
                                                                <td>
                                                                    @if ($project->student)
                                                                        <i class="fas fa-user text-primary me-1"></i>
                                                                        {{ $project->student->name }}
                                                                        </a>
                                                                    @else
                                                                        <span
                                                                            class="text-muted">{{ __('Non assigné') }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3">{{ __('Encadreur') }}</th>
                                                                <td>
                                                                    @php
                                                                        $supervisor = null;
                                                                        $supervisorId = null;

                                                                        // Vérifier d'abord si le projet a un superviseur direct
                                                                        if ($project->supervisor) {
                                                                            $supervisor = $project->supervisor;
                                                                            $supervisorId = $project->supervisor_id;
                                                                        }
                                                                        // Vérifier ensuite dans les affectations
                                                                        elseif (
                                                                            $project->student &&
                                                                            isset($project->student->assignments) &&
                                                                            $project->student->assignments
                                                                        ) {
                                                                            $assignment =
                                                                                $project->student->assignments;
                                                                            if (
                                                                                $assignment &&
                                                                                isset($assignment->supervisor_id)
                                                                            ) {
                                                                                $supervisorId =
                                                                                    $assignment->supervisor_id;
                                                                                $supervisor = \App\Models\User::find(
                                                                                    $supervisorId,
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    @if ($supervisor)
                                                                        <div class="d-flex align-items-center">

                                                                            <i
                                                                                class="fas fa-user-tie text-success me-1"></i>
                                                                            {{ $supervisor->name }}
                                                                            </a>
                                                                        </div>
                                                                    @else
                                                                        <div class="d-flex align-items-center">
                                                                            <span
                                                                                class="text-muted me-2">{{ __('Non assigné') }}</span>

                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3">{{ __('Date de création') }}</th>
                                                                <td>{{ $project->created_at->format('d/m/Y à H:i') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3">{{ __('Date de mise à jour') }}</th>
                                                                <td>{{ $project->updated_at->format('d/m/Y à H:i') }}</td>
                                                            </tr>
                                                            @if ($project->status == 'rejected' && $project->rejection_reason)
                                                                <tr>
                                                                    <th class="ps-3">{{ __('Motif du rejet') }}</th>
                                                                    <td class="text-danger">
                                                                        {{ $project->rejection_reason }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">{{ __('Actions') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.documents', ['project_id' => $project->id]) }}"
                                                class="btn btn-outline-primary">
                                                <i class="fas fa-file-alt me-1"></i> {{ __('Voir les documents') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
