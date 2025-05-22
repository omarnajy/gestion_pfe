<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Project;
use App\Models\Supervisor;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function index(Request $request)
{
    // Récupérer les affectations actuelles avec relations
    $currentAssignments = Assignment::with(['student', 'supervisor', 'project'])
        ->when($request->has('supervisor_id'), function($query) use ($request) {
            return $query->where('supervisor_id', $request->supervisor_id);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // Récupérer les étudiants non affectés
    $unassignedStudents = User::where('role', 'student')
        ->whereDoesntHave('assignments', function($query) {
            $query->whereColumn('student_id', 'users.id');
        })
        ->get();
        
    // Récupérer les encadreurs avec le nombre EXACT d'étudiants encadrés
    $supervisors = User::where('role', 'supervisor')
        ->get()
        ->map(function($supervisor) {
            // Compter directement les affectations pour ce superviseur
            $studentsCount = Assignment::where('supervisor_id', $supervisor->id)->count();
            $supervisor->students_count = $studentsCount;
            return $supervisor;
        });

    // Encadreurs qui peuvent encore accepter des étudiants
    $availableSupervisors = $supervisors->filter(function($supervisor) {
        return $supervisor->students_count < $supervisor->max_students;
    });

    \Log::info('Statistiques des encadreurs:');
    foreach ($supervisors as $supervisor) {
        \Log::info("- {$supervisor->name}: {$supervisor->students_count}/{$supervisor->max_students} étudiants");
    }

    return view('admin.assignments', compact(
        'currentAssignments',
        'unassignedStudents',
        'supervisors',
        'availableSupervisors'
    ));
}

    public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:users,id',
        'supervisor_id' => 'required|exists:users,id',
    ]);

    $student = User::where('id', $request->student_id)
                  ->where('role', 'student')
                  ->first();
                  
    $supervisor = User::where('id', $request->supervisor_id)
                     ->where('role', 'supervisor')
                     ->first();

    if (!$student) {
        return back()->with('error', 'L\'étudiant sélectionné n\'existe pas.');
    }
    
    if (!$supervisor) {
        return back()->with('error', 'L\'encadreur sélectionné n\'existe pas.');
    }

    // Vérifier si l'étudiant est déjà affecté
    $existingAssignment = Assignment::where('student_id', $student->id)->first();
    if ($existingAssignment) {
        return back()->with('error', 'Cet étudiant est déjà affecté à un encadreur.');
    }

    // Vérifier si l'encadreur a atteint sa limite
    if ($supervisor->assignments()->count() >= $supervisor->max_students) {
        return back()->with('error', 'Cet encadreur a atteint son nombre maximum d\'étudiants.');
    }

    // Trouver le projet de l'étudiant s'il existe
    $project = Project::where('student_id', $student->id)->first();
    
    // Ne pas créer de projet si l'étudiant n'en a pas
    if (!$project) {
        // Option 1: Empêcher l'affectation si l'étudiant n'a pas de projet
        // return back()->with('error', 'Cet étudiant n\'a pas de projet. Impossible de créer une affectation.');
        
        // Option 2: Créer une affectation sans projet
        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'project_id' => null // Laisser le champ project_id à NULL
        ]);
        
        return redirect()->route('admin.assignments')
            ->with('success', 'Affectation créée avec succès. Notez que l\'étudiant n\'a pas de projet.');
    } else {
        // Mettre à jour le superviseur du projet existant
        $project->supervisor_id = $supervisor->id;
        $project->save();
        
        // Créer l'affectation avec le projet existant
        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'project_id' => $project->id
        ]);
        
        return redirect()->route('admin.assignments')
            ->with('success', 'Affectation créée avec succès. Le projet existant de l\'étudiant a été assigné à l\'encadreur.');
    }
}


    public function update(Request $request, Assignment $assignment)
{
    $request->validate([
        'supervisor_id' => 'required|exists:users,id',
    ]);

    // Récupérer le superviseur
    $supervisor = User::where('id', $request->supervisor_id)
                    ->where('role', 'supervisor')
                    ->first();
                    
    if (!$supervisor) {
        return back()->with('error', 'L\'encadreur sélectionné n\'existe pas.');
    }

    // Vérifier si l'encadreur peut accepter plus d'étudiants
    if ($supervisor->id != $assignment->supervisor_id) {
        $supervisor_assignments_count = Assignment::where('supervisor_id', $supervisor->id)->count();
        if ($supervisor_assignments_count >= $supervisor->max_students) {
            return back()->with('error', 'Cet encadreur a atteint son nombre maximum d\'étudiants.');
        }
    }

    // Récupérer l'ancien et le nouveau superviseur pour le log
    $oldSupervisorName = User::find($assignment->supervisor_id)->name ?? 'Inconnu';
    $newSupervisorName = $supervisor->name;

    // Mettre à jour l'affectation
    $assignment->supervisor_id = $supervisor->id;
    $assignment->save();

    // Mettre à jour le superviseur dans le projet associé
    $project = null;
    if ($assignment->project_id) {
        $project = Project::find($assignment->project_id);
        if ($project) {
            $project->supervisor_id = $supervisor->id;
            $project->save();
        }
    }
    // Dans la méthode update, avant de renvoyer la réponse:
\Log::info('Mise à jour de l\'affectation #' . $assignment->id . ': ' . 
    'Étudiant: ' . $assignment->student->name . ', ' . 
    'Ancien encadreur: ' . $oldSupervisorName . ', ' . 
    'Nouvel encadreur: ' . $newSupervisorName);

if ($project) {
    \Log::info('Projet #' . $project->id . ' mis à jour avec le nouvel encadreur: ' . $newSupervisorName);
}

    return redirect()->route('admin.assignments')
        ->with('success', 'Affectation mise à jour avec succès. L\'encadreur a été changé de "' . $oldSupervisorName . '" à "' . $newSupervisorName . '"');
}

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('admin.assignments')->with('success', 'Affectation supprimée avec succès.');
    }
}