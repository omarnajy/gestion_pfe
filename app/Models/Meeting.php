<?php
// app/Models/Meeting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'scheduled_at',
        'location',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getParticipantsAttribute()
    {
        $participants = [];
        
        if ($this->project) {
            if ($this->project->student) {
                $participants[] = $this->project->student;
            }
            
            if ($this->project->supervisor) {
                $participants[] = $this->project->supervisor;
            }
        }
        
        return collect($participants);
    }
}
