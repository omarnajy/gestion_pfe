<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'department',
        'specialty',
        'is_active',    
        'student_id',  
        'field',        
        'max_students',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'max_students' => 'integer',
    ];

    /**
 * Get students assigned to this supervisor through assignments.
 */
public function assignedStudents()
{
    return $this->belongsToMany(User::class, 'assignments', 'supervisor_id', 'student_id');
}

public function hasSupervisor()
{
    // Un étudiant a un encadreur s'il a un projet avec un superviseur
    return $this->hasOne(Project::class, 'student_id')->whereNotNull('supervisor_id');
}

    public function projectsAsStudent()
    {
        return $this->hasMany(Project::class, 'student_id');
    }

    public function projectsAsSupervisor()
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    public function pendingProjects()
    {
        return $this->hasMany(Project::class)->where('status', 'pending');
    }

    //pour supervisors
    public function supervisedAssignments()
    {
        return $this->hasMany(Assignment::class, 'supervisor_id');
    }

    // Pour les étudiants
    public function assignments()
    {
        return $this->hasOne(Assignment::class, 'student_id');
    }

    /**
 * Get the uploaded documents for the user.
 */
public function uploadedDocuments()
{
    return $this->hasMany(Document::class, 'uploaded_by');
}

/**
 * Get the project of this student.
 */
public function project()
{
    return $this->hasOne(\App\Models\Project::class, 'student_id');
}

/**
 * Get the project applications from this student.
 */
public function projectApplications()
{
    return $this->hasMany(ProjectApplication::class, 'student_id');
}

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
