@extends('layouts.admin') {{-- ou le layout que tu utilises --}}

@section('title', 'Profil Admin')

@section('admin-content')
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Profil Administrateur</h1>

        <p>Bienvenue sur la page de profil de l’administrateur.</p>

        {{-- Exemple d’affichage des informations utilisateur --}}
        <ul>
            <li><strong>Nom :</strong> {{ auth()->user()->name }}</li>
            <li><strong>Email :</strong> {{ auth()->user()->email }}</li>
            {{-- Ajoute d’autres infos si besoin --}}
        </ul>
    </div>
@endsection
