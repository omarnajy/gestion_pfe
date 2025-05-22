@extends('layouts.supervisor') {{-- ou le layout que tu utilises --}}

@section('title', 'Profil Superviseur')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Profil du Superviseur</h1>

    <div class="mb-4">
        <strong>Nom :</strong> {{ $user->name }}
    </div>
    <div class="mb-4">
        <strong>Email :</strong> {{ $user->email }}
    </div>
    <div class="mb-4">
        <strong>Téléphone :</strong> {{ $user->phone ?? 'Non renseigné' }}
    </div>
    <div class="mb-4">
        <strong>Bio :</strong> {{ $user->bio ?? 'Aucune description' }}
    </div>

    {{-- Ajoute un lien pour modifier le profil si besoin --}}
    <a href="{{ route('supervisor.profile') }}" class="text-blue-600 hover:underline">Modifier le profil</a>
</div>
@endsection
