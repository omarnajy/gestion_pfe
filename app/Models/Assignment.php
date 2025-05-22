<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'project_id',
    ];

    /**
     * Get the student that owns the assignment.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the supervisor that owns the assignment.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the project associated with the assignment.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Dans le modèle Assignment.php
protected static function booted()
{
    static::updated(function ($assignment) {
        // Si le supervisor_id a changé
        if ($assignment->isDirty('supervisor_id')) {
            // Mettre à jour tous les projets associés à cette assignation
            if ($assignment->project_id) {
                Project::where('id', $assignment->project_id)
                       ->update(['supervisor_id' => $assignment->supervisor_id]);
            }
            
            // Mettre à jour tous les projets de l'étudiant qui pourraient ne pas être 
            // directement associés à cette assignation
            Project::where('student_id', $assignment->student_id)
                   ->update(['supervisor_id' => $assignment->supervisor_id]);
        }
    });
}
}