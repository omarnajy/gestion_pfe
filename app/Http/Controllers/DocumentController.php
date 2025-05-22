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
    public function index()
    {
        $documents = Document::with(['project', 'project.student', 'project.supervisor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request, Project $project)
    {
        // Validate the request
        $request->validate([
            'document' => 'required|file|max:10240|mimes:pdf,doc,docx,ppt,pptx',
            'type' => 'required|string|max:255',
        ]);

        // Ensure the current user owns this project
        if ($project->student_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à ajouter des documents à ce projet');
        }

        // Handle file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('documents/' . $project->id, 'public');
            
            // Create new document record
            $document = new Document();
            $document->project_id = $project->id;
            $document->path = $path;
            $document->original_name = $originalName;
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
    
    // Retournez le fichier pour le téléchargement
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
            $document->original_name ?? ($document->name . '.' . pathinfo($document->path, PATHINFO_EXTENSION))
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