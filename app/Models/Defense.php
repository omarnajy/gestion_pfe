<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defense extends Model
{
    //
    protected $fillable = [
        'project_id',
        'date',
        'time',
        'location',
        'duration',
        'jury_members', // JSON des membres du jury
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'jury_members' => 'array'
    ];

        public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
