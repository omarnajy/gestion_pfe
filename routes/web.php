<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\IsStudent;
use App\Http\Middleware\IsSupervisor;
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

// Route d'accueil avec sélection du rôle
Route::get('/', [AuthController::class, 'showRoleSelection'])->name('home');

// Routes d'authentification
Route::get('/login/{role}', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');
//Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route de déconnexion (accessible à tous les utilisateurs authentifiés)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard général (redirige vers le dashboard spécifique selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes pour les notifications (communes à tous les rôles)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});

// Routes pour les étudiants
Route::prefix('student')->middleware(['auth', \App\Http\Middleware\IsStudent::class])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    
    // Gestion des projets
    Route::get('/projects', [StudentController::class, 'projectIndex'])->name('student.projects.index');
    Route::get('/projects/available', [StudentController::class, 'availableProjects'])->name('student.projects.available');
    Route::post('/projects/{project}/apply', [StudentController::class, 'applyToProject'])->name('student.projects.apply');
    Route::get('/projects/create', [StudentController::class, 'projectCreate'])->name('student.projects.create');
    Route::post('/projects', [StudentController::class, 'projectStore'])->name('student.projects.store');
    Route::get('/projects/{project}', [StudentController::class, 'projectShow'])->name('student.projects.show');

    // NOUVELLE ROUTE POUR LA RESOUMISSION
    Route::put('/projects/{project}/resubmit', [StudentController::class, 'resubmitProject'])->name('student.projects.resubmit');
    Route::put('/projects/{project}/update', [StudentController::class, 'projectUpdate'])->name('student.projects.update');

    // Show Evaluation
    Route::get('/evaluation', [StudentController::class, 'showEvaluation'])->name('student.evaluation.show');

    // Gestion des documents
    Route::post('/projects/{project}/documents', [StudentController::class, 'storeDocument'])->name('student.documents.store');
    Route::get('/projects/{project}/documents/{document}/download', [StudentController::class, 'downloadDocument'])->name('student.documents.download');
    Route::delete('/projects/{project}/documents/{document}', [StudentController::class, 'destroyDocument'])->name('student.documents.destroy');
    
    // Commentaires et remarques
    Route::post('comments/{projectId}', [StudentController::class, 'storeComment'])->name('student.comments.store');
    Route::delete('comments/{comment}', [StudentController::class, 'destroyComment'])->name('student.comments.destroy');

    //Profile
    Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::put('/student/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    Route::put('/profile/password', [StudentController::class, 'updatePassword'])->name('student.update-password');
    Route::put('/profile/preferences', [StudentController::class, 'updatePreferences'])->name('student.update-preferences');
    Route::get('/student/profile/edit', [StudentController::class, 'editProfile'])->name('student.profile.edit');
});

// Routes pour les encadreurs
Route::prefix('supervisor')->middleware(['auth', \App\Http\Middleware\IsSupervisor::class])->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
    
     // Gestion des étudiants encadrés
     Route::get('/students', [SupervisorController::class, 'students'])->name('supervisor.students.index');
        
     // Gestion des projets
     Route::get('/projects', [SupervisorController::class, 'projectIndex'])->name('supervisor.projects.index');
     Route::get('/projects/{project}', [SupervisorController::class, 'projectShow'])->name('supervisor.projects.show');
     Route::put('/projects/{project}/validate', [SupervisorController::class, 'approveProject'])->name('supervisor.projects.validate');
     Route::put('/projects/{project}/reject', [SupervisorController::class, 'rejectProject'])->name('supervisor.projects.reject');
     Route::post('/projects', [SupervisorController::class, 'projectStore'])->name('supervisor.projects.store');
     
     // Gestion des commentaires et remarques
     Route::get('/projects/{project}/comments', [SupervisorController::class, 'storeComment'])->name('supervisor.comments.index');
     Route::post('/projects/{project}/comments', [SupervisorController::class, 'storeComment'])->name('supervisor.comments.store');
     Route::delete('/comments/{comment}', [SupervisorController::class, 'destroyComment'])->name('supervisor.comments.destroy');
     
     // Évaluation
     Route::get('/projects/{project}/evaluate', [SupervisorController::class, 'showEvaluationForm'])->name('supervisor.evaluation.form');
     Route::post('/projects/{project}/evaluate', [SupervisorController::class, 'storeEvaluation'])->name('supervisor.evaluation.store');
     Route::put('/projects/{project}/evaluate', [SupervisorController::class, 'storeEvaluation'])->name('supervisor.projects.evaluate');
     
     // Téléchargement des documents
     Route::get('/projects/{project}/documents', [SupervisorController::class, 'documents'])->name('supervisor.documents.index');
     Route::get('/projects/{project}/documents/{document}/download', [DocumentController::class, 'download'])->name('supervisor.documents.download');

     //Profile
    Route::get('/profile', [SupervisorController::class, 'profile'])->name('supervisor.profile');
    Route::put('/supervisor/profile/update', [SupervisorController::class, 'updateProfile'])->name('supervisor.profile.update');
    Route::put('/profile/password', [SupervisorController::class, 'updatePassword'])->name('supervisor.update-password');
    Route::put('/profile/preferences', [SupervisorController::class, 'updatePreferences'])->name('supervisor.update-preferences');
    Route::get('/supervisor/profile/edit', [SupervisorController::class, 'editProfile'])->name('supervisor.profile.edit');
});

// Routes pour les administrateurs
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Gestion des utilisateurs
    Route::get('/users', [AdminController::class, 'userIndex'])->name('admin.users.index'); // Route ajoutée pour l'index des utilisateurs
    
    // Création et modification spécifique par rôle
    Route::get('/users/{id}', [AdminController::class, 'userShow'])->name('admin.users.show');
    Route::get('/users/create/{role}', [AdminController::class, 'userCreate'])->name('admin.users.create');
    Route::post('/users/store/{role?}', [AdminController::class, 'userStore'])->name('admin.users.store');
    Route::get('/users/edit/{id}', [AdminController::class, 'userEdit'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'userUpdate'])->name('admin.users.update');
    Route::delete('/users/destroy/{id}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');
    
    // Affectation encadreur-étudiant
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('admin.assignments');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('admin.assignments.store');
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('admin.assignments.update');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('admin.assignments.destroy');
    Route::get('/admin/sync-projects-assignments', [App\Http\Controllers\AdminController::class, 'syncProjectsAndAssignments'])->name('admin.sync-projects-assignments');
    
    // Validation finale des PFEs
    Route::get('/projects', [AdminController::class, 'projectIndex'])->name('admin.projects.index');
    Route::put('/projects/{project}/approve', [AdminController::class, 'approveProject'])->name('admin.projects.approve');
    Route::put('/projects/{project}/validate', [AdminController::class, 'validateProject'])->name('admin.projects.validate');
    Route::get('/projects/{project}', [AdminController::class, 'projectShow'])->name('admin.projects.show');
    Route::put('/projects/{project}/reject', [AdminController::class, 'rejectProject'])->name('admin.projects.reject');

    // Évaluation
    Route::get('/evaluations', [AdminController::class, 'evaluationsIndex'])->name('admin.evaluations.index');
    
    // Statistiques
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('admin.statistics');
    Route::get('/statistics/projects-by-supervisor', [StatisticsController::class, 'projectsBySupervisor'])->name('admin.statistics.projects-by-supervisor');
    Route::get('/statistics/projects-by-status', [StatisticsController::class, 'projectsByStatus'])->name('admin.statistics.projects-by-status');
    Route::get('/statistics/projects-by-field', [StatisticsController::class, 'projectsByField'])->name('admin.statistics.projects-by-field');
    
    // Gestion des documents (tous projets)
    Route::get('/documents', [DocumentController::class, 'index'])->name('admin.documents');
    Route::get('/documents/{document}/download', [DocumentController::class, 'admindownload'])->name('admin.documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('admin.documents.destroy');
    // Nouvelle route pour voir les documents d'un projet spécifique
    Route::get('/projects/{project}/documents', [DocumentController::class, 'index'])->name('admin.projects.documents');

    //Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/supervisor/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('admin.update-password');
    Route::put('/profile/preferences', [AdminController::class, 'updatePreferences'])->name('admin.update-preferences');
    Route::get('/supervisor/profile/edit', [AdminController::class, 'editProfile'])->name('admin.profile.edit');

    //Notification-Soutenance
    Route::get('/defenses', [AdminController::class, 'defensesIndex'])->name('admin.defenses.index');
    Route::get('/defenses/create', [AdminController::class, 'defenseCreate'])->name('admin.defenses.create');
    Route::post('/defenses', [AdminController::class, 'defenseStore'])->name('admin.defenses.store');
    Route::get('/defenses/{defense}/edit', [AdminController::class, 'defenseEdit'])->name('admin.defenses.edit');
    Route::put('/defenses/{defense}', [AdminController::class, 'defenseUpdate'])->name('admin.defenses.update');

});

// Routes partagées pour les projets
Route::middleware(['auth'])->group(function () {
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/timeline', [ProjectController::class, 'timeline'])->name('projects.timeline');
});