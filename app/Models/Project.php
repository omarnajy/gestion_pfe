<?php
// app/Models/Project.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'objectives',
        'technologies',
        'keywords',
        'status',
        'student_id',
        'supervisor_id',
        'start_date',
        'end_date',
        'final_grade',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function assignment()
    {
        return $this->hasOne(Assignment::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
 * Get the applications for this project.
 */
public function applications()
{
    return $this->hasMany(ProjectApplication::class);
}

 public function isAvailableForApplications()
    {
        return $this->is_proposed_by_supervisor && $this->student_id === null && $this->status === 'available';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'available' => 'Disponible',
            default => ucfirst($this->status),
        };
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function documents()
{
    return $this->hasMany(Document::class);
}

public function timeline()
{
    return $this->hasMany(TimelineEvent::class)->orderBy('created_at', 'asc');
}

    public function getCompletionPercentageAttribute()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100);
    }

    // Dans app/Models/Project.php

/**
 * Get the color class for the status badge.
 *
 * @return string
 */
public function getStatusColorAttribute()
{
    return match($this->status) {
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'in_progress' => 'info',
        'completed' => 'primary',
        default => 'secondary',
    };
}

}