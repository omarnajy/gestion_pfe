<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'student_id',
        'motivation',
        'status',
        'feedback',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /** 
     * Get the project this application is for.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the student who applied.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}