<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $totalProjects = Project::count();
        $pendingProjects = Project::where('status', 'pending')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $approvedProjects = Project::where('status', 'approved')->count();
        $rejectedProjects = Project::where('status', 'rejected')->count();
        $projects = Project::with(['student', 'supervisor'])->latest()->take(5)->get();
        $userCount = User::count();
        $studentsWithoutSupervisor = User::where('role', 'student')
        ->whereDoesntHave('assignments')
        ->get()
        ->count();
        $studentsWithoutSupervisorDetails = User::where('role', 'student')
        ->whereDoesntHave('assignments')
        ->whereDoesntHave('projectsAsStudent', function($query) {
            $query->whereNotNull('supervisor_id');
        })
        ->get(['id', 'name', 'email']);

        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalSupervisors', 
            'totalProjects', 
            'pendingProjects', 
            'completedProjects', 
            'approvedProjects', 
            'rejectedProjects',
            'projects',
            'userCount',
            'studentsWithoutSupervisor',
            'studentsWithoutSupervisorDetails'
        ));
    }
    
    public function departmentStatistics()
    {
        $departments = User::whereNotNull('department')
            ->distinct('department')
            ->pluck('department');
            
        $departmentStats = [];
        
        foreach ($departments as $department) {
            $students = User::where('role', 'student')
                ->where('department', $department)
                ->count();
                
            $supervisors = User::where('role', 'supervisor')
                ->where('department', $department)
                ->count();
                
            $projects = Project::whereHas('student', function($query) use ($department) {
                $query->where('department', $department);
            })->count();
            
            $completedProjects = Project::whereHas('student', function($query) use ($department) {
                $query->where('department', $department);
            })->where('status', 'completed')->count();
            
            $departmentStats[] = [
                'name' => $department,
                'students' => $students,
                'supervisors' => $supervisors,
                'projects' => $projects,
                'completed_projects' => $completedProjects,
                'completion_rate' => $projects > 0 ? round(($completedProjects / $projects) * 100, 2) : 0
            ];
        }
        
        return view('admin.dashboard', compact('departmentStats'));
    }
    
    public function projectDistribution()
    {
        $supervisors = User::where('role', 'supervisor')->get();
        $projectDistribution = [];
        
        foreach ($supervisors as $supervisor) {
            $count = Project::where('supervisor_id', $supervisor->id)->count();
            $activeCount = Project::where('supervisor_id', $supervisor->id)
                ->whereIn('status', ['approved', 'in_progress'])
                ->count();
                
            $projectDistribution[] = [
                'supervisor' => $supervisor,
                'total_projects' => $count,
                'active_projects' => $activeCount
            ];
        }
        
        // Trier par nombre de projets décroissant
        usort($projectDistribution, function($a, $b) {
            return $b['total_projects'] <=> $a['total_projects'];
        });
        
        return view('admin.dashboard', compact('projectDistribution'));
    }

    public function userShow($id)
{
    $user = User::findOrFail($id);
    return view('admin.users.show', compact('user'));
}
    
    // Gestion des utilisateurs
    public function userIndex()
    {
       // Vérifiez que vos utilisateurs sont bien récupérés
    $users = User::orderBy('role')->orderBy('name')->get();
    
    // Préparez les variables pour chaque onglet
    $allUsers = $users;
    $students = $users->where('role', 'student');
    $supervisors = $users->where('role', 'supervisor');
    $admins = $users->where('role', 'admin');

    // Pour déboguer - vérifier les données
  //dd($allUsers, $students, $supervisors, $admins);
    
    return view('admin.users.index', compact('allUsers', 'students', 'supervisors', 'admins'));
    }
    
    public function userCreate($role)
{
    // Selon le rôle passé en paramètre, on adapte le label et la variable role
    switch ($role) {
        case 'supervisor':
            $roleLabel = 'Encadreur';
            break;
        case 'student':
            $roleLabel = 'Étudiant';
            break;
        case 'admin':
            $roleLabel = 'Administrateur';
            break;
        default:
            $roleLabel = 'Utilisateur';
            break;
    }

    return view('admin.users.create', compact('roleLabel', 'role'));
}
    
//  la méthode userStore  :

public function userStore(Request $request, $role = null)
{
    // Validation des données de base commune à tous les rôles
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
       
    ];
    
    // Règles de validation spécifiques selon le rôle
    if ($role == 'student') {
        //$rules['student_id'] = 'required|string|max:255';
        $rules['field'] = 'required|string|max:255';
    } elseif ($role == 'supervisor') {
        $rules['specialty'] = 'required|string|max:255';
        $rules['department'] = 'required|string|max:255';
        $rules['max_students'] = 'required|integer|min:1|max:20';
    }
    
    $validated = $request->validate($rules);
    
    // Préparer les données pour la création de l'utilisateur
    $userData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $role, // Utilisation du rôle passé en paramètre
        'created_at' => now(), // Définir explicitement created_at
        'updated_at' => now(), // Définir explicitement updated_at
    ];
    
    // Ajouter les données spécifiques au rôle
    if ($role == 'student') {
        //$userData['student_id'] = $validated['student_id'];
        $userData['field'] = $validated['field'];
        // On considère que le département est le même que la filière pour un étudiant
        $userData['department'] = $validated['field'];
    } elseif ($role == 'supervisor') {
        $userData['specialty'] = $validated['specialty'];
        $userData['department'] = $validated['department'];
        $userData['max_students'] = $validated['max_students'];
    }
    
    // Créer l'utilisateur
    $user = User::create($userData);
    
    return redirect()->route('admin.users.index')
        ->with('success', 'Utilisateur créé avec succès.');
}
    
    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    
    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:student,supervisor,admin',
            'department' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
        
        // Vérifier si le rôle a changé
        $roleChanged = $user->role !== $validated['role'];
        
        $user->update($validated);
        
        // Gérer le changement de rôle
        if ($roleChanged) {
            if ($validated['role'] === 'student') {
                // Si l'utilisateur devient étudiant, supprimer ses projets en tant que superviseur
                Project::where('supervisor_id', $user->id)->update(['supervisor_id' => null]);
            } elseif ($validated['role'] === 'supervisor') {
                // Si l'utilisateur devient superviseur, supprimer ses projets en tant qu'étudiant
                Project::where('student_id', $user->id)->delete();
            }
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }
    
    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        
        // Ne pas supprimer l'administrateur courant
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        // Supprimer l'utilisateur
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
    
    public function userResetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Générer un nouveau mot de passe
        $password = Str::random(10);
        $user->password = Hash::make($password);
        $user->save();
        
        // Notifier l'utilisateur par email (à implémenter)
        // Mail::to($user->email)->send(new PasswordReset($user, $password));
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Mot de passe réinitialisé avec succès. Le nouveau mot de passe est : ' . $password);
    }
    
    // Gestion des rôles
    public function userRoles()
    {
        $students = User::where('role', 'student')->orderBy('name')->get();
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();
        $admins = User::where('role', 'admin')->orderBy('name')->get();
        
        return view('admin.users.roles', compact('students', 'supervisors', 'admins'));
    }
    
    // Gestion des projets
    public function projectIndex(Request $request)
{
    // Initialiser la requête
    $query = Project::with(['student', 'supervisor']);
    
    // Filtre de recherche
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('student', function($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('supervisor', function($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }
    
    // Filtre par département
    if ($request->has('department') && !empty($request->department)) {
        $department = $request->department;
        $query->whereHas('student', function($q) use ($department) {
            $q->where('department', $department);
        });
    }
    
    // Filtre par statut (maintenu pour compatibilité avec l'ancien code)
    if ($request->has('status') && !empty($request->status)) {
        $query->where('status', $request->status);
    }
    
    // Récupérer les projets paginés
    $projects = $query->latest()->paginate(10);
    
    // Formater les projets pour l'affichage
    foreach ($projects as $project) {
        $statusColorMap = [
            'pending' => 'warning',
            'approved' => 'success',
            'validated' => 'success',
            'rejected' => 'danger',
            'completed' => 'primary',
            'in_progress' => 'info',
        ];
        
        $statusTextMap = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'validated' => 'Validé',
            'rejected' => 'Rejeté',
            'completed' => 'Terminé',
            'in_progress' => 'En cours',
        ];
        
        $project->status_color = $statusColorMap[$project->status] ?? 'secondary';
        $project->status_text = $statusTextMap[$project->status] ?? $project->status;
    }
    
    // Récupérer les superviseurs pour le modal d'assignation
    $supervisors = \App\Models\User::where('role', 'supervisor')
        ->withCount(['projectsAsSupervisor as active_projects_count' => function($query) {
            $query->whereIn('status', ['approved', 'in_progress']);
        }])
        ->orderBy('name')
        ->get();
    
    return view('admin.projects.index', compact('projects', 'supervisors'));
}
    
    public function projectShow($id)
    {
        $project = Project::with(['student', 'supervisor', 'tasks', 'comments.user', 'files', 'evaluations'])
            ->findOrFail($id);
        $supervisors = User::where('role', 'supervisor')
        ->withCount('projectsAsSupervisor as projects_count')
        ->orderBy('name')
        ->get();
            
        return view('admin.projects.show', compact('project'));
    }
    
public function rejectProject(Request $request, $projectId)
{
    $project = Project::findOrFail($projectId);
    
    $validated = $request->validate([
        'rejection_reason' => 'required|string',
    ]);
    
    $project->status = 'rejected';
    $project->rejection_reason = $validated['rejection_reason'];
    $project->save();
    
    // Notifier l'étudiant si nécessaire
    // ...
    
    return redirect()->route('admin.projects.show', $project->id)
        ->with('success', 'Projet rejeté avec succès.');
}

    public function projectAssignments()
    {
        $unassignedProjects = Project::whereNull('supervisor_id')
            ->orWhere('supervisor_id', 0)
            ->with('student')
            ->get();
            
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();
        
        return view('admin.assignments', compact('unassignedProjects', 'supervisors'));
    }

    /**
 * Synchroniser les tables projects et assignments
 * 
 * @return \Illuminate\Http\RedirectResponse
 */
public function syncProjectsAndAssignments()
{
    // 1. Mettre à jour les projets selon les affectations
    $assignments = \App\Models\Assignment::all();
    foreach ($assignments as $assignment) {
        // Trouver tous les projets de cet étudiant et les mettre à jour
        \App\Models\Project::where('student_id', $assignment->student_id)
            ->whereNull('supervisor_id')
            ->update(['supervisor_id' => $assignment->supervisor_id]);
    }
    
    // 2. Créer des affectations basées sur les projets s'il n'en existe pas
    $projects = \App\Models\Project::whereNotNull('supervisor_id')->get();
    foreach ($projects as $project) {
        \App\Models\Assignment::updateOrCreate(
            ['student_id' => $project->student_id],
            ['supervisor_id' => $project->supervisor_id]
        );
    }
    
    return redirect()->back()->with('success', 'Synchronisation terminée avec succès.');
}
    
    public function assignSupervisor(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);
        
        // Vérifier que l'utilisateur est bien un superviseur
        $supervisor = User::where('id', $validated['supervisor_id'])
            ->where('role', 'supervisor')
            ->firstOrFail();
            
        $project->supervisor_id = $supervisor->id;
        $project->save();
        
        // Notifier l'étudiant
        Notification::create([
            'user_id' => $project->student_id,
            'title' => 'Encadreur assigné',
            'message' => 'Un encadreur a été assigné à votre projet : ' . $supervisor->name,
            'type' => 'info',
            'notifiable_id' => $project->id,
            'notifiable_type' => Project::class,
        ]);
        
        // Notifier le superviseur
        Notification::create([
            'user_id' => $supervisor->id,
            'title' => 'Nouveau projet à encadrer',
            'message' => 'Vous avez été assigné comme encadreur au projet : ' . $project->title,
            'type' => 'info',
            'notifiable_id' => $project->id,
            'notifiable_type' => Project::class,
        ]);
        
        return redirect()->route('admin.assignments')
            ->with('success', 'Encadreur assigné avec succès.');
    }

    public function showAssignments()
    {
        // Logique pour afficher les affectations (assignments)
        // Par exemple récupérer les données nécessaires
        // $assignments = Assignment::all();

        return view('admin.assignments' /*, compact('assignments')*/);
    }
    
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile',compact('user'));; // ou autre vue appropriée
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Profil mis à jour avec succès.');
    }
    
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Le mot de passe actuel est incorrect.');
                }
            }],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Mot de passe changé avec succès.');
    }

    public function editProfile()
    {
        $user = auth()->user();
        return view('admin.profile_edit', compact('user'));
    }

    // Paramètres généraux
    public function generalSettings()
    {
        // Implémenter la logique de paramètres (à étendre)
        return view('admin.settings.general');
    }
    
    public function academicYearSettings()
    {
        // Implémenter la logique de l'année académique (à étendre)
        return view('admin.settings.academic-year');
    }
    
    public function deadlineSettings()
    {
        // Implémenter la logique des échéances (à étendre)
        return view('admin.settings.deadlines');
    }
    
    // Statistiques générales
    public function statistics()
    {
        // Projets par statut
        $projectStats = DB::table('projects')
            ->select(DB::raw('status, count(*) as count'))
            ->groupBy('status')
            ->get();
            
        // Projets par département
        $departmentStats = DB::table('projects')
            ->join('users', 'projects.student_id', '=', 'users.id')
            ->select(DB::raw('users.department, count(*) as count'))
            ->whereNotNull('users.department')
            ->groupBy('users.department')
            ->get();
            
        // Charge de travail par encadreur
        $supervisorStats = DB::table('projects')
            ->join('users', 'projects.supervisor_id', '=', 'users.id')
            ->select(DB::raw('users.name, count(*) as count'))
            ->whereNotNull('projects.supervisor_id')
            ->groupBy('users.name')
            ->orderByDesc('count')
            ->get();
        
        return view('admin.statistics.index', compact('projectStats', 'departmentStats', 'supervisorStats'));
    }
}