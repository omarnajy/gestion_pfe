@extends('layouts.admin')

@section('admin-content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Gestion des Soutenances</h1>
                <p class="text-muted">Programmez et gérez les soutenances de PFE</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.defenses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Programmer une soutenance
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th>Étudiant</th>
                                <th>Date & Heure</th>
                                <th>Lieu</th>
                                <th>Jury</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($defenses ?? [] as $defense)
                                <tr>
                                    <td>{{ $defense->project->title }}</td>
                                    <td>{{ $defense->project->student->name }}</td>
                                    <td>
                                        {{ $defense->date->format('d/m/Y') }}<br>
                                        <small>{{ $defense->time->format('H:i') }}</small>
                                    </td>
                                    <td>{{ $defense->location }}</td>
                                    <td>
                                        @foreach ($defense->jury_members as $jury)
                                            <span class="badge bg-info">{{ $jury['name'] }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-gavel fa-3x text-muted mb-3"></i>
                                        <p>Aucune soutenance programmée</p>
                                        <a href="{{ route('admin.defenses.create') }}" class="btn btn-primary">
                                            Programmer la première soutenance
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
