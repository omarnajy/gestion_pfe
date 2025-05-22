<?php

// app/Http/Controllers/ProjectController.php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function downloadSubmission($id)
    {
        $submission = Submission::findOrFail($id);
        $project = Project::findOrFail($submission->project_id);
        
        // Vérifier que l'utilisateur est autorisé à télécharger ce document
        if (Auth::user()->isStudent() && $project->student_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        if (Auth::user()->isSupervisor() && $project->supervisor_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        return Storage::download($submission->file_path, $submission->title);
    }
    
    public function updateTaskStatus(Request $request, $taskId)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,overdue',
        ]);
        
        $task = Task::findOrFail($taskId);
        $project = Project::findOrFail($task->project_id);
        
        // Vérifier que l'utilisateur est autorisé à modifier cette tâche
        if (Auth::user()->isStudent() && $project->student_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        if (Auth::user()->isSupervisor() && $project->supervisor_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        $task->status = $request->status;
        
        if ($request->status == 'completed') {
            $task->completed_at = now();
        }
        
        $task->save();
        
        if (Auth::user()->isStudent()) {
            return redirect()->route('student.project.show', $project->id)
                ->with('success', 'Statut de la tâche mis à jour');
        } else {
            return redirect()->route('supervisor.project.show', $project->id)
                ->with('success', 'Statut de la tâche mis à jour');
        }
    }
    
    public function searchProjects(Request $request)
    {
        $search = $request->input('search');
        
        if (Auth::user()->isAdmin()) {
            $projects = Project::where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->with(['student', 'supervisor'])
                ->get();
                
            return view('admin.projects.index', compact('projects'));
            
        } elseif (Auth::user()->isSupervisor()) {
            $projects = Project::where('supervisor_id', Auth::id())
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->get();
                
            return view('supervisor.projects.index', compact('projects'));
            
        } else {
            $projects = Project::where('student_id', Auth::id())
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->get();
                
            return view('student.projects.index', compact('projects'));
        }
    }

    public function store(Request $request)
{
    // Validation des données
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'domain' => 'required|string',
        'supervisor_id' => 'required|exists:users,id',
        // Autres règles de validation...
    ]);
    $studentId = auth()->id();
    // Ajouter le supervisor_id automatiquement
    $validated['supervisor_id'] = auth()->id();
    // Par défaut, pas d'étudiant assigné
    $validated['student_id'] = null;
    $validated['status'] = 'pending';
    
    // Création du projet
    $project = Project::create([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'domain' => $validated['domain'],
        'supervisor_id' => Auth::id(),
        'student_id' => $studentId,
        'status' => 'pending',
        // Autres champs...
    ]);
    
    return redirect()->route('supervisor.projects.index')
        ->with('success', 'Sujet de PFE proposé avec succès et en attente de validation.');
}
    
    public function getProjectStatistics($id)
    {
        $project = Project::findOrFail($id);
        
        // Vérifier les autorisations
        if (Auth::user()->isStudent() && $project->student_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        if (Auth::user()->isSupervisor() && $project->supervisor_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        // Collecter les statistiques du projet
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $pendingTasks = $project->tasks()->where('status', 'pending')->count();
        $inProgressTasks = $project->tasks()->where('status', 'in_progress')->count();
        $overdueTasks = $project->tasks()->where('status', 'overdue')->count();
        
        $totalSubmissions = $project->submissions()->count();
        $reviewedSubmissions = $project->submissions()->where('status', 'reviewed')->count();
        $pendingSubmissions = $project->submissions()->where('status', 'pending')->count();
        
        $commentsCount = $project->comments()->count();
        
        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        // Calcul des jours restants jusqu'à la date de fin si elle est définie
        $daysRemaining = null;
        if ($project->end_date) {
            $endDate = \Carbon\Carbon::parse($project->end_date);
            $daysRemaining = $endDate->diffInDays(now(), false); // Négatif si dépassé
        }
        
        $statistics = [
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'inProgressTasks' => $inProgressTasks,
            'overdueTasks' => $overdueTasks,
            'totalSubmissions' => $totalSubmissions,
            'reviewedSubmissions' => $reviewedSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'commentsCount' => $commentsCount,
            'progressPercentage' => $progressPercentage,
            'daysRemaining' => $daysRemaining,
        ];
        
        if (Auth::user()->isStudent()) {
            return view('student.projects.statistics', compact('project', 'statistics'));
        } elseif (Auth::user()->isSupervisor()) {
            return view('supervisor.projects.statistics', compact('project', 'statistics'));
        } else {
            return view('admin.projects.statistics', compact('project', 'statistics'));
        }
    }
    
    public function exportProjectData($id)
    {
        $project = Project::with(['tasks', 'submissions', 'comments', 'student', 'supervisor'])
            ->findOrFail($id);
            
        // Vérifier les autorisations
        if (Auth::user()->isStudent() && $project->student_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        if (Auth::user()->isSupervisor() && $project->supervisor_id != Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        // Préparer les données pour l'export (format PDF ou CSV selon les besoins)
        $data = [
            'project' => $project,
            'tasks' => $project->tasks,
            'submissions' => $project->submissions,
            'comments' => $project->comments,
            'student' => $project->student,
            'supervisor' => $project->supervisor,
        ];
        
        // Générer un PDF (exemple avec une bibliothèque comme dompdf)
        // ou un CSV selon les besoins
        // Pour cet exemple, on va simplement télécharger un JSON
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="project-'.$id.'.json"')
            ->header('Content-Type', 'application/json');
    }
    
}
