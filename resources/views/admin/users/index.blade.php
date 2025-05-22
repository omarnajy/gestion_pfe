{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('admin-content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestion des Utilisateurs</h1>
        </div>
        <div class="col-md-4 text-end">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="createUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-plus"></i> Créer un utilisateur
                </button>
                <ul class="dropdown-menu" aria-labelledby="createUserDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.users.create', 'student') }}">Étudiant</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.users.create', 'supervisor') }}">Encadreur</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.users.create', 'admin') }}">Administrateur</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="userTypesTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">Tous</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" aria-controls="students" aria-selected="false">Étudiants</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="supervisors-tab" data-bs-toggle="tab" data-bs-target="#supervisors" type="button" role="tab" aria-controls="supervisors" aria-selected="false">Encadreurs</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins" type="button" role="tab" aria-controls="admins" aria-selected="false">Administrateurs</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="userTypesContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    @include('admin.users.partials.users-table', ['users' => $allUsers ?? []])
                </div>
                <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab">
                    @include('admin.users.partials.users-table', ['users' => $students ?? []])
                </div>
                <div class="tab-pane fade" id="supervisors" role="tabpanel" aria-labelledby="supervisors-tab">
                    @include('admin.users.partials.users-table', ['users' => $supervisors ?? []])
                </div>
                <div class="tab-pane fade" id="admins" role="tabpanel" aria-labelledby="admins-tab">
                    @include('admin.users.partials.users-table', ['users' => $admins ?? []])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection