<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'student_id',
        'field',
    ];

    /**
     * Get the assignment for the student.
     */
    public function assignment()
    {
        return $this->hasOne(Assignment::class);
    }

    /**
     * Get the project for the student.
     */
    public function project()
    {
        return $this->hasOne(Project::class);
    }
}