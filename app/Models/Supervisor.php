<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'specialty',
        'department',
        'max_students',
    ];

    // Exemple : relation un superviseur a plusieurs projets
    public function projects()
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    // Si tu veux compter les projets en attente
    public function pendingProjects()
    {
        return $this->projects()->where('status', 'pending');
    }

    // Projets terminés
    public function completedProjects()
    {
        return $this->projects()->where('status', 'completed');

    }

    /**
     * Get the students assigned to the supervisor.
     */
    public function students()
    {
        return $this->hasMany(Assignment::class);
    }
}
