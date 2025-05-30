<?php
// app/Http/Controllers/StudentController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\File;
use App\Models\User;
use App\Models\Comment;
use App\Models\Document;
use App\Models\Notification;
use App\Models\ProjectApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $comments = collect();
        $documents = collect();
        

        $project = Project::where('student_id', $user->id)->first();
        $projects = Project::where('student_id', $user->id)->with('supervisor')->latest()->get();

        // Définir $hasProject : true s’il y a au moins un projet, sinon false
        $hasProject = !is_null($project);

        if ($hasProject) {
            // Récupérer les commentaires du projet
            $comments = $project->comments()->with('user')->latest()->get();

            // Récupérer les documents du projet
            $documents = $project->documents()->latest()->get();

            // Ajouter les attributs calculés au projet
            $this->addProjectAttributes($project);
        }

        return view('student.dashboard', compact(
            'hasProject',
            'project',
            'comments',
            'documents'
        ));
    }

    public function statistics()
    {
        $user = Auth::user();
        $projects = $user->projectsAsStudent()->with(['tasks', 'milestones', 'files'])->get();

        return view('student.dashboard', compact('projects'));
    }

    public function projectIndex()
    {
        $user = Auth::user();
        $projects = Project::where('student_id', $user->id)->with('supervisor')->latest()->get();

        return view('student.projects.index', compact('projects'));
    }

    /**
     * Affiche les détails d'un projet.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function projectShow($id)
    {
        $project = Project::where('student_id', Auth::id())
            ->with(['supervisor', 'comments.user', 'documents', 'milestones'])
            ->findOrFail($id);

        // Ajouter les attributs calculés au projet
        $this->addProjectAttributes($project);

        return view('student.projects.show', compact('project'));
    }

    /**
     * Affiche le formulaire de création d'un projet.
     *
     * @return \Illuminate\View\View
     */
    public function projectCreate()
    {
        // Vérifier si l'étudiant a déjà un projet
        $hasProject = Auth::user()->projectsAsStudent()->exists();

        if ($hasProject) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Vous avez déjà un projet en cours.');
        }

        return view('student.projects.create');
    }

    /**
     * Enregistre un nouveau projet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function projectStore(Request $request)
    {
        // Vérifier si l'étudiant a déjà un projet
        $hasProject = Auth::user()->projectsAsStudent()->exists();

        if ($hasProject) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Vous avez déjà un projet en cours.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'nullable|string|max:255',
            'technologies' => 'nullable|string|max:255',
        ]);

        $project = new Project();
        $project->title = $validated['title'];
        $project->description = $validated['description'];
        $project->keywords = $validated['keywords'] ?? null;
        $project->technologies = $validated['technologies'] ?? null;
        $project->student_id = Auth::id();
        $project->status = 'pending';
        $project->save();

        return redirect()->route('student.dashboard')
            ->with('success', 'Votre projet a été soumis avec succès. Il est maintenant en attente de validation.');
    }
    public function projectEdit($id)
    {
        $project = Project::where('student_id', Auth::id())->findOrFail($id);

        // Vérifier si le projet peut encore être modifié
        if (!in_array($project->status, ['draft', 'pending', 'rejected'])) {
            return redirect()->route('student.projects.show', $project->id)
                ->with('error', 'Ce projet ne peut plus être modifié.');
        }

        return view('student.projects.index', compact('project'));
    }

    /**
 * Met à jour un projet existant
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function projectUpdate(Request $request, $id)
{
    $project = Project::where('student_id', Auth::id())->findOrFail($id);
    
    // Vérifier si le projet peut encore être modifié
    if (!in_array($project->status, ['draft', 'pending', 'rejected'])) {
        return redirect()->route('student.projects.show', $project->id)
            ->with('error', 'Ce projet ne peut plus être modifié car il a été validé.');
    }
    
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:10',
        'keywords' => 'nullable|string|max:255',
        'technologies' => 'nullable|string|max:255',
    ]);
    
    // Mettre à jour le projet
    $project->update($validated);
    
    // Si le projet était rejeté et qu'il est modifié, garder le statut rejeté
    // L'étudiant devra cliquer sur "Soumettre à nouveau" pour changer le statut
    
    // Ajouter un commentaire pour indiquer la modification
    Comment::create([
        'project_id' => $project->id,
        'user_id' => Auth::id(),
        'content' => 'Projet modifié par l\'étudiant.',
        'is_feedback' => false,
    ]);
    
    return redirect()->route('student.projects.index')
        ->with('success', 'Projet mis à jour avec succès. N\'oubliez pas de le soumettre à nouveau pour validation.');
}

    /**
 * Resoumets un projet rejeté après modification
 *
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function resubmitProject($id)
{
    $project = Project::where('student_id', Auth::id())->findOrFail($id);
    
    // Vérifier que le projet est rejeté
    if ($project->status !== 'rejected') {
        return redirect()->back()->with('error', 'Seuls les projets rejetés peuvent être resoumis.');
    }
    
    // Changer le statut à "pending" pour une nouvelle validation
    $project->status = 'pending';
    $project->save();
    
    // Ajouter un commentaire pour indiquer la resoumission
    Comment::create([
        'project_id' => $project->id,
        'user_id' => Auth::id(),
        'content' => 'Projet resoumis pour une nouvelle évaluation après modification.',
        'is_feedback' => false,
    ]);
    
    // Notifier le superviseur si assigné
    if ($project->supervisor_id) {
        Notification::create([
            'user_id' => $project->supervisor_id,
            'title' => 'Projet resoumis',
            'message' => 'L\'étudiant ' . Auth::user()->name . ' a resoumis le projet "' . $project->title . '" pour une nouvelle évaluation.',
            'type' => 'info',
            'notifiable_id' => $project->id,
            'notifiable_type' => Project::class,
        ]);
    }
    
    return redirect()->route('student.projects.index')
        ->with('success', 'Projet resoumis avec succès. Il est maintenant en attente de validation.');
}

    /**
     * Ajoute des attributs calculés au projet.
     *
     * @param \App\Models\Project $project
     * @return void
     */
    private function addProjectAttributes(Project $project)
    {
        // Calculer le status_color
        $project->status_color = match ($project->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };

        // Calculer le status_label
        $project->status_label = match ($project->status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            default => 'Inconnu',
        };

    }

    /**
     * Affiche la liste des projets disponibles proposés par les encadreurs.
     *
     * @return \Illuminate\View\View
     */
    public function availableProjects()
    {
        // Récupérer les projets proposés par les encadreurs qui ne sont pas encore assignés
        $projects = Project::where('is_proposed_by_supervisor', true)
            ->whereNull('student_id')
            ->with('supervisor')
            ->latest()
            ->paginate(10);

        return view('student.projects.available', compact('projects'));
    }

    /**
     * Traite la candidature à un projet proposé par un encadreur.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $projectId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyToProject(Request $request, $projectId)
    {
        // Vérifier si l'étudiant a déjà un projet
        $hasProject = Auth::user()->projectsAsStudent()->exists();

        if ($hasProject) {
            return redirect()->route('student.projects.available')
                ->with('error', 'Vous avez déjà un projet en cours. Vous ne pouvez pas postuler à un autre projet.');
        }

        $project = Project::findOrFail($projectId);

        // Vérifier si le projet est toujours disponible
        if ($project->student_id !== null) {
            return redirect()->route('student.projects.available')
                ->with('error', 'Ce projet n\'est plus disponible.');
        }

        // Vérifier si l'étudiant a déjà postulé pour ce projet
        $existingApplication = ProjectApplication::where('project_id', $projectId)
            ->where('student_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()->route('student.projects.available')
                ->with('error', 'Vous avez déjà postulé pour ce projet.');
        }

        $validated = $request->validate([
            'motivation' => 'required|string|min:10',
        ]);
        // Assigner directement le projet à l'étudiant
        $project->student_id = Auth::id();
        $project->status = 'pending'; // Le projet passe en attente de validation par le superviseur
        $project->save();

        // Créer une candidature
        $application = new ProjectApplication();
        $application->project_id = $project->id;
        $application->student_id = Auth::id();
        $application->motivation = $validated['motivation'];
        $application->status = 'accepted';
        $application->save();

        // Notifier l'encadreur
        if ($project->supervisor_id) {
            // Code pour notifier l'encadreur (si vous avez un système de notification)
            // Notification::create([
            //     'user_id' => $project->supervisor_id,
            //     'title' => 'Nouvelle candidature',
            //     'message' => 'Un étudiant a postulé pour votre projet "' . $project->title . '".',
            //     'type' => 'info',
            // ]);
        }

        return redirect()->route('student.projects.index')
            ->with('success', 'Votre candidature a été soumise avec succès. Vous serez notifié lorsque l\'encadreur l\'aura examinée.');
    }

    public function uploadFile(Request $request, $projectId)
    {
        $project = Project::where('student_id', Auth::id())->findOrFail($projectId);

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,zip,rar|max:20480', // 20MB max
            'type' => 'required|string',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . $originalName;
        $path = $file->storeAs('project_files', $fileName, 'public');

        // Vérifier si un fichier du même type existe déjà pour incrémenter la version
        $version = 1;
        $existingFile = File::where('project_id', $project->id)
            ->where('type', $request->type)
            ->orderBy('version', 'desc')
            ->first();

        if ($existingFile) {
            $version = $existingFile->version + 1;
        }

        $fileRecord = File::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'filename' => $fileName,
            'original_filename' => $originalName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'type' => $request->type,
            'version' => $version,
        ]);

        // Notifier l'encadreur si assigné
        if ($project->supervisor_id) {
            Notification::create([
                'user_id' => $project->supervisor_id,
                'title' => 'Nouveau fichier soumis',
                'message' => 'L\'étudiant ' . Auth::user()->name . ' a soumis un nouveau fichier pour le projet "' . $project->title . '".',
                'type' => 'info',
                'notifiable_id' => $fileRecord->id,
                'notifiable_type' => File::class,
            ]);
        }

        return redirect()->route('student.projects.show', $project->id)
            ->with('success', 'Fichier téléversé avec succès.');
    }

    /**
     * Téléverse un nouveau document pour un projet.
     */
    /**
     * Téléverse un nouveau document pour un projet.
     */
    public function storeDocument(Request $request, $projectId)
    {
        $project = Project::where('student_id', Auth::id())->findOrFail($projectId);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:rapport,presentation,cahier_charges,autre',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240', // max 10MB
        ]);

        // Générer un nom de fichier unique
        $fileName = time() . '_' . Str::slug($request->name) . '.' . $request->file->extension();

        // Enregistrer le fichier dans le stockage
        $path = $request->file->storeAs('projects/' . $project->id . '/documents', $fileName, 'public');

        // Créer l'enregistrement du document en utilisant uniquement les colonnes existantes
        $document = new Document();
        $document->project_id = $project->id;
        $document->name = $request->name;
        $document->type = $request->type;
        $document->path = $path;
        $document->size = $request->file->getSize();
        $document->save();

        return redirect()->back()->with('success', 'Document téléversé avec succès.');
    }

    /**
     * Télécharge un document spécifique.
     *
     * @param  int  $projectId
     * @param  int  $documentId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDocument($projectId, $documentId)
    {
        $project = Project::where('student_id', Auth::id())->findOrFail($projectId);
        $document = $project->documents()->findOrFail($documentId);

        // Vérifier si le fichier existe dans le stockage
        if (!Storage::disk('public')->exists($document->path)) {
            return redirect()->back()->with('error', 'Le fichier demandé n\'existe plus.');
        }

        // Retourner le fichier pour le téléchargement
        return response()->download(Storage::disk('public')->path($document->path), $document->name . '.' . $document->extension);
    }

    /**
     * Supprime un document spécifique.
     *
     * @param  int  $projectId
     * @param  int  $documentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyDocument($projectId, $documentId)
    {
        $project = Project::where('student_id', Auth::id())->findOrFail($projectId);
        $document = $project->documents()->findOrFail($documentId);

        // Supprimer le fichier du stockage
        if (Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        // Supprimer l'enregistrement de la base de données
        $document->delete();

        return redirect()->back()->with('success', 'Document supprimé avec succès.');
    }

    /**
     * Enregistre un nouveau commentaire pour un projet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projectId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeComment(Request $request, $projectId)
    {
        $project = Project::where('student_id', Auth::id())->findOrFail($projectId);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->project_id = $project->id;
        $comment->user_id = Auth::id();
        $comment->content = $validated['content'];
        $comment->is_feedback = false;
        $comment->save();

        return redirect()->back()->with('success', 'Commentaire ajouté avec succès.');
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
    $project = Project::where('student_id', Auth::id())->findOrFail($comment->project_id);
    
    // Vérifier que l'étudiant est le propriétaire du commentaire
    if ($comment->user_id !== Auth::id()) {
        return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à supprimer ce commentaire.');
    }
    
    // Supprimer le commentaire
    $comment->delete();
    
    return redirect()->back()->with('success', 'Commentaire supprimé avec succès.');
}

    public function profile()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
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

        return redirect()->route('student.dashboard')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    public function editProfile()
    {
        $user = auth()->user();
        return view('student.profile_edit', compact('user'));
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

        return redirect()->route('student.profile')->with('success', 'Mot de passe changé avec succès.');
    }

/**
 * Affiche l'évaluation du projet de l'étudiant
 */
public function showEvaluation()
{
    $user = Auth::user();
    $project = Project::where('student_id', $user->id)->first();
    
    if (!$project) {
        return redirect()->route('student.dashboard')
            ->with('error', 'Vous n\'avez pas de projet assigné.');
    }
    
    $evaluation = $project->evaluations()->latest()->first();
    
    return view('student.evaluation.show', compact('project', 'evaluation'));
}
}
