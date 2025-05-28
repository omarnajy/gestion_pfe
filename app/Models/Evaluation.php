<?php
// Mise à jour complète de app/Models/Evaluation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'evaluator_id',
        'presentation_grade',
        'documentation_grade',
        'grade',
    ];

    protected $casts = [
        'presentation_grade' => 'decimal:2',
        'documentation_grade' => 'decimal:2',
        'grade' => 'decimal:2',
    ];

    /**
     * Relation avec le projet
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relation avec l'évaluateur (encadreur)
     */
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Calculer automatiquement la note finale
     */
    public function calculateFinalGrade()
    {
        $this->grade = ( $this->presentation_grade + $this->documentation_grade) / 2;
        return $this->grade;
    }

    /**
     * Obtenir le libellé du type d'évaluation
     */
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'milestone' => 'Évaluation intermédiaire',
            'final' => 'Évaluation finale',
            default => 'Non défini'
        };
    }

    /**
     * Obtenir la couleur selon la note
     */
    public function getGradeColorAttribute()
    {
        if ($this->grade >= 16) return 'success';
        if ($this->grade >= 12) return 'warning';
        if ($this->grade >= 10) return 'info';
        return 'danger';
    }

    /**
     * Vérifier si l'évaluation est réussie
     */
    public function isSuccessful()
    {
        return $this->grade >= 10;
    }
}