<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class DocumentController extends Controller
{
    /**
     * Display a listing of the documents (admin only).
     */
    public function index(Request $request)
    {
        $query = Document::with(['project', 'project.student', 'project.supervisor']);

        // Filtrage par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%")
                  ->orWhereHas('project', function($projectQuery) use ($search) {
                      $projectQuery->where('title', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('project.student', function($studentQuery) use ($search) {
                      $studentQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('project.supervisor', function($supervisorQuery) use ($search) {
                      $supervisorQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filtrage par type de fichier
        if ($request->filled('file_type')) {
            $query->where('type', $request->file_type);
        }

        // Filtrage par projet spécifique (si nécessaire)
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request, Project $project)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,ppt,pptx',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255', // Nom donné par l'étudiant
        ]);

        // Ensure the current user owns this project
        if ($project->student_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à ajouter des documents à ce projet');
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('documents/' . $project->id, 'public');
            
            // Create new document record
            $document = new Document();
            $document->project_id = $project->id;
            $document->path = $path;
            $document->name = $request->name; // Nom donné par l'étudiant
            $document->original_name = $originalName; // Nom original du fichier
            $document->type = $request->type;
            $document->size = $file->getSize();
            $document->mime_type = $file->getMimeType();
            $document->save();

            // Create notification for supervisor
            if ($project->supervisor) {
                $notification = new \App\Models\Notification();
                $notification->user_id = $project->supervisor->id;
                $notification->content = "Un nouveau document a été téléversé pour le projet \"" . $project->title . "\"";
                $notification->link = route('supervisor.projects.show', $project->id);
                $notification->save();
            }

            return redirect()->back()->with('success', 'Document téléversé avec succès');
        }

        return redirect()->back()->with('error', 'Erreur lors du téléversement du document');
    }

    /**
     * Download a document.
     */
    public function download($projectId, $documentId)
    {
        $document = Document::findOrFail($documentId);
        $project = Project::findOrFail($projectId);

        // Check permissions based on role
        $user = auth()->user();
        $canDownload = false;

        if ($user->role == 'student' && $project->student_id == $user->id) {
            $canDownload = true;
        } elseif ($user->role == 'supervisor' && $project->supervisor_id == $user->id) {
            $canDownload = true;
        } elseif ($user->role == 'admin') {
            $canDownload = true;
        }

        if (!$canDownload) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à télécharger ce document');
        }

        // Vérifiez si le fichier existe dans le stockage
        if (!Storage::disk('public')->exists($document->path)) {
            return back()->with('error', 'Le fichier demandé n\'existe plus.');
        }
        
        return response()->download(
            Storage::disk('public')->path($document->path), 
            $document->name . '.' . pathinfo($document->path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Admin specific download route.
     */
    public function adminDownload($documentId)
    {
        $document = Document::findOrFail($documentId);

        // Check if admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($document->path)) {
            return redirect()->back()->with('error', 'Le fichier demandé n\'existe pas');
        }

        return response()->download(
            Storage::disk('public')->path($document->path), 
            $document->name . '.' . pathinfo($document->path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Delete a document (admin only).
     */
    public function destroy(Document $document)
    {
        // Check if admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        // Delete file
        if (Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        // Delete record
        $document->delete();

        return redirect()->back()->with('success', 'Document supprimé avec succès');
    }
}