<?php
// app/Http/Controllers/SupervisorController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Evaluation;
use App\Models\Notification;
use App\Models\Meeting;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $project = Project::first();
        $projects = Project::where('supervisor_id', auth()->id())->get();
        $notifications = $user->notifications()->latest()->take(5)->get();
        $pendingProjectsCollection = Project::where('supervisor_id', $user->id)
                                      ->where('status', 'pending')
                                      ->with(['student'])
                                      ->get();
        $pendingProjectsCount = $pendingProjectsCollection->count();
        $activeProjects = Project::where('status', 'active')->count();
        // Récupérer uniquement les projets de ce superviseur spécifique
       // 1. Via supervisor_id direct
       $directProjects = Project::where('supervisor_id', $user->id)->get();
    
        // 2. Via les affectations
        $studentIds = \App\Models\Assignment::where('supervisor_id', $user->id)
               ->pluck('student_id')
               ->toArray();
        $assignmentProjects = Project::whereIn('student_id', $studentIds)->get();
        // 3. Combiner et dédupliquer
        $allSupervisorProjects = $directProjects->concat($assignmentProjects)->unique('id');
    
        $totalProjects = $allSupervisorProjects->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $recentComments = Comment::whereHas('project', function($query) use ($user) {
                                $query->where('supervisor_id', $user->id);
                            })
                            ->with(['user', 'project'])
                            ->latest()
                            ->take(5)
                            ->get();
        $rejectedProjects = Project::where('status', 'rejected')->count();
        
        return view('supervisor.dashboard', compact('project','projects', 'notifications','activeProjects','totalProjects',
                    'completedProjects','recentComments','rejectedProjects',
                    'pendingProjectsCollection','pendingProjectsCount',));
    }
    
    public function studentsProgress()
    {
        $user = Auth::user();
        $projects = $user->projectsAsSupervisor()->with(['student', 'tasks', 'milestones'])->get();
        
        return view('supervisor.dashboard', compact('projects'));
    }
    
    public function upcomingDeadlines()
    {
        $user = Auth::user();
        $projects = $user->projectsAsSupervisor()->with(['student', 'milestones'])->get();
        
        $deadlines = [];
        foreach ($projects as $project) {
            foreach ($project->milestones as $milestone) {
                if ($milestone->status != 'completed') {
                    $deadlines[] = [
                        'project' => $project,
                        'milestone' => $milestone,
                    ];
                }
            }
        }
        
        // Trier par date d'échéance
        usort($deadlines, function($a, $b) {
            return $a['milestone']->due_date <=> $b['milestone']->due_date;
        });
        
        return view('supervisor.dashboard', compact('deadlines'));
    }
    
    public function projectIndex()
{
    $user = Auth::user();
    
    // 1. Récupérer les projets directement associés au superviseur
    $directProjects = Project::where('supervisor_id', $user->id)->with('student')->get();
    
    // 2. Récupérer les projets via les affectations
    $studentIds = \App\Models\Assignment::where('supervisor_id', $user->id)
        ->pluck('student_id')
        ->toArray();
    
    $assignmentProjects = Project::whereIn('student_id', $studentIds)
        ->with('student')
        ->get();
    
    // 3. Combiner et dédupliquer
    $allProjects = $directProjects->concat($assignmentProjects)->unique('id');
    
    // Log pour débogage
    \Log::info('Récupération des projets pour le superviseur #' . $user->id);
    \Log::info('Projets directs: ' . $directProjects->count());
    \Log::info('Projets via affectations: ' . $assignmentProjects->count());
    \Log::info('Total après déduplication: ' . $allProjects->count());
    
    // 4. Filtrer par statut
    $pendingProjects = $allProjects->where('status', 'pending');
    $validatedProjects = $allProjects->whereIn('status', ['approved', 'validated']); // Accepter les deux valeurs
    $rejectedProjects = $allProjects->where('status', 'rejected');
    
    // 5. Convertir en collection triée
    $projects = $allProjects->sortByDesc('created_at');
    
    return view('supervisor.projects.index', compact('projects', 'pendingProjects', 'validatedProjects', 'rejectedProjects'));
}
    
    public function projectShow($id)
{
    // Récupérer le projet sans filtrer par supervisor_id d'abord
    $project = Project::with(['student', 'tasks', 'comments.user', 'files', 'milestones', 'evaluations'])
        ->findOrFail($id);
        
    // Récupérer les projets via les affectations
    $userIsAssignedSupervisor = \App\Models\Assignment::where('supervisor_id', Auth::id())
        ->where(function($query) use ($project) {
            $query->where('project_id', $project->id)
                ->orWhere('student_id', $project->student_id);
        })
        ->exists();
        
    // Vérifier si l'utilisateur est autorisé à voir ce projet
    if ($project->supervisor_id != Auth::id() && !$userIsAssignedSupervisor) {
        // Log pour débogage
        \Log::warning('Tentative d\'accès non autorisé au projet #' . $id . ' par le superviseur #' . Auth::id());
        abort(403, 'Vous n\'êtes pas autorisé à voir ce projet.');
    }

    // Handle projects without students
    if (!$project->student) {
        $project->student = (object)[
            'name' => 'Non assigné',
            'email' => '-'
        ];
    }
    
    return view('supervisor.projects.show', compact('project'));
}

    public function projectStore(Request $request)
{
    $validated = $request->validate([
        // Vos règles de validation pour la création d'un projet
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'keywords' => 'nullable|string|max:255',
        'technologies' => 'nullable|string|max:255',
        // Autres champs requis
    ]);
    
    // Créer le projet
    $project = new Project();
    $project->title = $validated['title'];
    $project->description = $validated['description'];
    $project->keywords = $validated['keywords'] ?? null;
    $project->technologies = $validated['technologies'] ?? null;
    $project->supervisor_id = auth()->id();
    $project->student_id = null; // Explicitement NULL
    $project->is_proposed_by_supervisor = true;
    $project->status = 'pending';
    $project->save();
    
    return redirect()->route('supervisor.projects.index', $project->id)
        ->with('success', 'Projet créé avec succès.');
}
    
    public function approveProject($id)
{
    try {
        $project = Project::findOrFail($id);
        
        if ($project->status === 'pending') {
            $project->status = 'approved';
            // Ne pas utiliser validated_at
            
            if ($project->save()) {
                // Notifier l'étudiant si nécessaire
                
                return redirect()->route('supervisor.projects.show', $project->id)
                    ->with('success', 'Projet approuvé avec succès.');
            } else {
                return redirect()->back()->with('error', 'Erreur lors de la sauvegarde du projet.');
            }
        } else {
            return redirect()->back()->with('error', 'Ce projet ne peut pas être validé car il n\'est pas en attente.');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Une erreur est survenue lors de la validation du projet: ' . $e->getMessage());
    }
}
    
    
//la méthode rejectProject
public function rejectProject(Request $request, $id)
{
    try {
        // Récupérer le projet
        $project = Project::findOrFail($id);
        
        // Vérifier l'autorisation
        $userIsAssignedSupervisor = \App\Models\Assignment::where('supervisor_id', Auth::id())
            ->where(function($query) use ($project) {
                $query->where('project_id', $project->id)
                    ->orWhere('student_id', $project->student_id);
            })
            ->exists();
            
        if ($project->supervisor_id != Auth::id() && !$userIsAssignedSupervisor) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à rejeter ce projet.');
        }
        
        // Validation
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ], [
            'reason.required' => 'La raison du rejet est obligatoire.',
            'reason.min' => 'La raison doit contenir au moins 10 caractères.',
            'reason.max' => 'La raison ne peut pas dépasser 1000 caractères.',
        ]);
        
        // Vérifier que le projet peut être rejeté
        if (!in_array($project->status, ['pending', 'submitted'])) {
            return redirect()->back()->with('error', 'Ce projet ne peut pas être rejeté car il n\'est pas en attente.');
        }
        
        // Mettre à jour seulement le statut
        $project->status = 'rejected';
        
        if ($project->save()) {
            // Ajouter un commentaire avec la raison du rejet
            Comment::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'content' => 'Projet rejeté : ' . $validated['reason'],
                'is_feedback' => true,
            ]);
            
            // Notifier l'étudiant si un étudiant est assigné
            if ($project->student_id) {
                Notification::create([
                    'user_id' => $project->student_id,
                    'title' => 'Projet rejeté',
                    'message' => 'Votre projet "' . $project->title . '" a été rejeté. Veuillez consulter les commentaires pour plus de détails.',
                    'type' => 'warning',
                    'notifiable_id' => $project->id,
                    'notifiable_type' => Project::class,
                ]);
            }
            
            return redirect()->route('supervisor.projects.index')
                ->with('success', 'Projet rejeté avec succès.');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde du rejet.');
        }
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        \Log::error('Erreur lors du rejet du projet #' . $id . ' par le superviseur #' . Auth::id() . ': ' . $e->getMessage());
        return redirect()->back()->with('error', 'Une erreur est survenue lors du rejet du projet. Veuillez réessayer.');
    }
}
    
    public function addComment(Request $request, $projectId)
    {
        $project = Project::where('supervisor_id', Auth::id())->findOrFail($projectId);
        
        $validated = $request->validate([
            'content' => 'required|string',
            'is_feedback' => 'boolean',
        ]);
        
        $comment = Comment::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'is_feedback' => $request->has('is_feedback') ? true : false,
        ]);
        
        // Notifier l'étudiant
        Notification::create([
            'user_id' => $project->student_id,
            'title' => 'Nouveau commentaire de l\'encadreur',
            'message' => 'Votre encadreur a ajouté un commentaire à votre projet "' . $project->title . '".',
            'type' => 'info',
            'notifiable_id' => $comment->id,
            'notifiable_type' => Comment::class,
        ]);
        
        return redirect()->route('supervisor.projects.show', $project->id)
            ->with('success', 'Commentaire ajouté avec succès.');
    }

    /**
 * Affiche la page des commentaires d'un projet.
 *
 * @param int $projectId
 * @return \Illuminate\View\View
 */
public function comments($projectId)
{
    $project = Project::with(['comments.user', 'student'])
        ->findOrFail($projectId);
    
    $comments = $project->comments()
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    return view('supervisor.comments.index', compact('project', 'comments'));
}

    /**
 * Ajoute un commentaire ou une remarque à un projet.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $projectId
 * @return \Illuminate\Http\RedirectResponse
 */
public function storeComment(Request $request, $projectId)
{
    $project = Project::where('supervisor_id', Auth::id())->findOrFail($projectId);
    
    $validated = $request->validate([
        'content' => 'required|string|min:3',
    ]);
    
    $comment = new Comment();
    $comment->project_id = $project->id;
    $comment->user_id = Auth::id();
    $comment->content = $validated['content'];
    $comment->is_feedback = true; // Marquer comme feedback du superviseur
    $comment->save();
    
    // Notifier l'étudiant si nécessaire
    if ($project->student_id) {
        // Code pour envoyer une notification à l'étudiant
        // ...
    }
    
    return redirect()->route('supervisor.projects.show', $project->id)
        ->with('success', 'Commentaire ajouté avec succès.');
}

/**
 * Supprime un commentaire spécifique.
 *
 * @param  int  $commentId
 * @return \Illuminate\Http\RedirectResponse
 */
public function destroyComment($commentId)
{
    $comment = Comment::findOrFail($commentId);
    $project = Project::findOrFail($comment->project_id);
    
    // Vérifier les autorisations
    $userIsAssignedSupervisor = \App\Models\Assignment::where('supervisor_id', Auth::id())
        ->where(function($query) use ($project) {
            $query->where('project_id', $project->id)
                ->orWhere('student_id', $project->student_id);
        })
        ->exists();
        
    if ($project->supervisor_id != Auth::id() && !$userIsAssignedSupervisor) {
        return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à supprimer ce commentaire.');
    }
    
    // Vérifier que le superviseur est le propriétaire du commentaire
    if ($comment->user_id !== Auth::id()) {
        return redirect()->back()->with('error', 'Vous ne pouvez supprimer que vos propres commentaires.');
    }
    
    // Supprimer le commentaire
    $comment->delete();
    
    return redirect()->back()->with('success', 'Commentaire supprimé avec succès.');
}
    
    public function evaluateProject($id)
    {
        $project = Project::where('supervisor_id', Auth::id())
            ->with(['student', 'tasks', 'files', 'evaluations'])
            ->findOrFail($id);
        
        return view('supervisor.projects.evaluate', compact('project'));
    }
    
    public function storeEvaluation(Request $request, $projectId)
    {
        $project = Project::where('supervisor_id', Auth::id())->findOrFail($projectId);
        
        $validated = $request->validate([
            'technical_grade' => 'required|numeric|min:0|max:20',
            'presentation_grade' => 'required|numeric|min:0|max:20',
            'documentation_grade' => 'required|numeric|min:0|max:20',
            'feedback' => 'required|string',
            'type' => 'required|in:milestone,final',
        ]);
        
        $evaluation = Evaluation::create([
            'project_id' => $project->id,
            'evaluator_id' => Auth::id(),
            'technical_grade' => $validated['technical_grade'],
            'presentation_grade' => $validated['presentation_grade'],
            'documentation_grade' => $validated['documentation_grade'],
            'feedback' => $validated['feedback'],
            'type' => $validated['type'],
        ]);
        
        // Calculer la note totale
        $evaluation->calculateTotalGrade();
        
        // Si c'est une évaluation finale, mettre à jour la note du projet
        if ($validated['type'] === 'final') {
            $project->final_grade = $evaluation->total_grade;
            $project->status = 'completed';
            $project->completion_date = now();
            $project->save();
        }
        
        // Notifier l'étudiant
        Notification::create([
            'user_id' => $project->student_id,
            'title' => 'Nouvelle évaluation',
            'message' => 'Votre projet "' . $project->title . '" a été évalué par votre encadreur.',
            'type' => 'info',
            'notifiable_id' => $evaluation->id,
            'notifiable_type' => Evaluation::class,
        ]);
        
        return redirect()->route('supervisor.projects.show', $project->id)
            ->with('success', 'Évaluation enregistrée avec succès.');
    }
    
   public function students()
{
    $user = Auth::user();
    
    // Récupérer les étudiants directement via les affectations avec leurs projets
    $students = User::where('role', 'student')
        ->whereHas('assignments', function($query) use ($user) {
            $query->where('supervisor_id', $user->id);
        })
        ->with(['project' => function($query) {
            // Pour déboguer
            $query->withoutGlobalScopes(); // Ignorer toutes les contraintes globales
        }])
        ->get();
    
    // Log pour débogage
    \Log::info('Superviseur ' . $user->name . ' accède à ses étudiants. Nombre trouvé: ' . $students->count());
    foreach($students as $student) {
        // Vérifier si l'étudiant a un projet dans la base de données
        $projectExists = \App\Models\Project::where('student_id', $student->id)->exists();
        $projectViaRelation = $student->project;
        $projectId = $projectViaRelation ? $projectViaRelation->id : 'AUCUN';
        $routeExistsCheck = route('supervisor.projects.show', ['project' => $projectId], false);
        
        \Log::info("Étudiant {$student->name} - Projet existe: " . ($projectExists ? 'OUI' : 'NON') . 
               ", Via relation: " . ($projectViaRelation ? $projectViaRelation->title . " (ID: {$projectViaRelation->id})" : 'AUCUN') .
               ", Route check: " . ($routeExistsCheck ? 'OK' : 'ERREUR'));
    }
    
    // Récupérer les projets pour référence (pour compatibilité)
    $projects = $user->projectsAsSupervisor()->with('student')->get();
    
    return view('supervisor.students.index', compact('students', 'projects'));
}
    
    public function scheduleMeeting(Request $request, $projectId)
    {
        $project = Project::where('supervisor_id', Auth::id())->findOrFail($projectId);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'location' => 'nullable|string|max:255',
        ]);
        
        $meeting = Meeting::create([
            'project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'scheduled_at' => $validated['scheduled_at'],
            'location' => $validated['location'],
            'status' => 'scheduled',
        ]);
        
        // Notifier l'étudiant
        Notification::create([
            'user_id' => $project->student_id,
            'title' => 'Nouvelle réunion programmée',
            'message' => 'Votre encadreur a programmé une réunion pour le projet "' . $project->title . '" le ' . $meeting->scheduled_at->format('d/m/Y à H:i'),
            'type' => 'info',
            'notifiable_id' => $meeting->id,
            'notifiable_type' => Meeting::class,
        ]);
        
        return redirect()->route('supervisor.projects.show', $project->id)
            ->with('success', 'Réunion programmée avec succès.');
    }

    public function profile(Request $request)
{
    $user = $request->user(); // Récupère le superviseur connecté
    return view('supervisor.profile', compact('user'));
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

        return redirect()->route('supervisor.dashboard')
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

        return redirect()->route('supervisor.profile')->with('success', 'Mot de passe changé avec succès.');
    }

    public function editProfile()
    {
        $user = auth()->user();
        return view('supervisor.profile_edit', compact('user'));
    }

    public function completeMeeting(Request $request, $meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        $project = Project::where('supervisor_id', Auth::id())->findOrFail($meeting->project_id);
        
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);
        
        $meeting->status = 'completed';
        $meeting->notes = $validated['notes'] ?? $meeting->notes;
        $meeting->save();
        
        // Notifier l'étudiant
        Notification::create([
            'user_id' => $project->student_id,
            'title' => 'Suivi de réunion',
            'message' => 'Votre encadreur a marqué la réunion "' . $meeting->title . '" comme terminée et a ajouté des notes de réunion.',
            'type' => 'info',
            'notifiable_id' => $meeting->id,
            'notifiable_type' => Meeting::class,
        ]);
        
        return redirect()->route('supervisor.projects.show', $project->id)
            ->with('success', 'Réunion marquée comme terminée.');
    }
}