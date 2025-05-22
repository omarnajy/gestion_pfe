@extends('layouts.supervisor')

@section('title', 'Évaluation du projet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Évaluation des livrables - {{ $project->title }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-user-graduate"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Étudiant</span>
                                    <span class="info-box-number">{{ $project->student->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-tasks"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tâches en attente d'évaluation</span>
                                    <span class="info-box-number">{{ $pendingTasks->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($pendingTasks->count() > 0)
                        <ul class="nav nav-tabs" id="taskTabs" role="tablist">
                            @foreach($pendingTasks as $index => $pendingTask)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index == 0 || $pendingTask->id == request('task') ? 'active' : '' }}" 
                                       id="task-{{ $pendingTask->id }}-tab" 
                                       data-toggle="tab" 
                                       href="#task-{{ $pendingTask->id }}" 
                                       role="tab" 
                                       aria-controls="task-{{ $pendingTask->id }}" 
                                       aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                        {{ $pendingTask->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="tab-content mt-4" id="taskTabsContent">
                            @foreach($pendingTasks as $index => $pendingTask)
                                <div class="tab-pane fade {{ $index == 0 || $pendingTask->id == request('task') ? 'show active' : '' }}" 
                                     id="task-{{ $pendingTask->id }}" 
                                     role="tabpanel" 
                                     aria-labelledby="task-{{ $pendingTask->id }}-tab">
                                    
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>{{ $pendingTask->title }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h5>Description de la tâche :</h5>
                                                    <p>{!! nl2br(e($pendingTask->description)) !!}</p>
                                                    
                                                    <h5>Date d'échéance :</h5>
                                                    <p>{{ $pendingTask->due_date->format('d/m/Y') }}</p>
                                                    
                                                    <h5>Date de soumission :</h5>
                                                    <p>{{ $pendingTask->updated_at->format('d/m/Y H:i') }}</p>
                                                    
                                                    @if($pendingTask->file)
                                                        <h5>Fichier soumis :</h5>
                                                        <p>
                                                            <a href="{{ asset('storage/' . $pendingTask->file) }}" class="btn btn-primary" target="_blank">
                                                                <i class="fas fa-download"></i> Télécharger le livrable
                                                            </a>
                                                        </p>
                                                    @endif
                                                    
                                                    @if($pendingTask->submission_note)
                                                        <h5>Note de l'étudiant :</h5>
                                                        <div class="callout callout-info">
                                                            <p>{!! nl2br(e($pendingTask->submission_note)) !!}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="card card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Grille d'évaluation</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <form action="{{ route('supervisor.projects.evaluate.submit', $pendingTask->id) }}" method="POST">
                                                                @csrf
                                                                
                                                                <div class="form-group">
                                                                    <label for="grade-{{ $pendingTask->id }}">Note (sur 20)</label>
                                                                    <input type="number" name="grade" id="grade-{{ $pendingTask->id }}" class="form-control" min="0" max="20" step="0.5" required>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label for="comment-{{ $pendingTask->id }}">Commentaire</label>
                                                                    <textarea name="comment" id="comment-{{ $pendingTask->id }}" class="form-control" rows="6" required></textarea>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label>Décision</label>
                                                                    <div class="custom-control custom-radio">
                                                                        <input class="custom-control-input" type="radio" id="approve-{{ $pendingTask->id }}" name="status" value="approved" checked>
                                                                        <label for="approve-{{ $pendingTask->id }}" class="custom-control-label">Approuver</label>
                                                                    </div>
                                                                    <div class="custom-control custom-radio">
                                                                        <input class="custom-control-input" type="radio" id="reject-{{ $pendingTask->id }}" name="status" value="rejected">
                                                                        <label for="reject-{{ $pendingTask->id }}" class="custom-control-label">Rejeter</label>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="mt-4">
                                                                    <button type="submit" class="btn btn-warning btn-block">
                                                                        <i class="fas fa-save"></i> Soumettre l'évaluation
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Information</h5>
                            Aucune tâche en attente d'évaluation pour ce projet.
                        </div>
                        
                        <a href="{{ route('supervisor.projects.show', $project->id) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Retour au projet
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tâches déjà évaluées -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Évaluations précédentes</h3>
                </div>
                <div class="card-body">
                    @if($evaluatedTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tâche</th>
                                        <th>Date d'évaluation</th>
                                        <th>Note</th>
                                        <th>Statut</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evaluatedTasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>{{ $task->evaluation->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $task->evaluation->grade }}/20</td>
                                            <td>
                                                @if($task->status == 'approved')
                                                    <span class="badge bg-success">Approuvé</span>
                                                @elseif($task->status == 'rejected')
                                                    <span class="badge bg-danger">Rejeté</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($task->evaluation->comment, 100) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucune tâche évaluée pour ce projet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection