<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'evaluator_id',
        'technical_grade',
        'presentation_grade',
        'documentation_grade',
        'grade',
        'feedback',
        'type',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function calculateTotalGrade()
    {
        $this->total_grade = ($this->technical_grade * 0.5) + 
                            ($this->presentation_grade * 0.25) + 
                            ($this->documentation_grade * 0.25);
        $this->save();
        
        return $this->total_grade;
    }

}
