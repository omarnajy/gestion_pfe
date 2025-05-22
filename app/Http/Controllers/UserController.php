<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index()
{
    // Redirigez vers la méthode userIndex du AdminController pour assurer la cohérence
    return app(AdminController::class)->userIndex();
}

    // Affiche le formulaire de création d’un utilisateur
    public function create()
    {
        $roleLabel = 'Utilisateur'; // ou 'Étudiant', 'Administrateur', selon le contexte
        $role = 'student'; // ou 'admin', 'student', selon le contexte

        return view('admin.users.create', compact('roleLabel','role')); // Crée cette vue si elle n’existe pas
    }

    public function store(Request $request, $role = null)
{
    // Rediriger vers la méthode userStore du AdminController
    return app(AdminController::class)->userStore($request, $role);
}

}
