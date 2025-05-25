<?php
// app/Models/Comment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'project_id',
        'is_feedback',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer ce commentaire
     */
    public function canBeDeletedBy($user)
    {
        // L'auteur du commentaire peut le supprimer
        if ($this->user_id === $user->id) {
            return true;
        }

        // L'admin peut supprimer tous les commentaires
        if ($user->role === 'admin') {
            return true;
        }

        // Le superviseur du projet peut supprimer les commentaires du projet
        if ($user->role === 'supervisor' && $this->project->supervisor_id === $user->id) {
            return true;
        }

        return false;
    }
}