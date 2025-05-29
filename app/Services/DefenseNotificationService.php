<?php

namespace App\Services;

use App\Models\Defense;
use App\Models\Notification;

class DefenseNotificationService
{
    public function notifyDefenseScheduled(Defense $defense)
    {
        $project = $defense->project;
        
        // Notifier l'étudiant
        $this->createNotification(
            $project->student_id,
            'Soutenance programmée',
            $this->getDefenseMessage($defense),
            'defense_scheduled',
            $defense
        );
        
        // Notifier l'encadreur
        if ($project->supervisor_id) {
            $this->createNotification(
                $project->supervisor_id,
                'Soutenance programmée pour votre étudiant',
                $this->getDefenseMessage($defense, 'supervisor'),
                'defense_scheduled',
                $defense
            );
        }
    }

    private function createNotification($userId, $title, $message, $type, $defense)
    {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => [
                'defense_id' => $defense->id,
                'date' => $defense->date->format('Y-m-d'),
                'time' => $defense->time->format('H:i:s'),
                'location' => $defense->location,
                'duration' => $defense->duration,
                'jury_members' => $defense->jury_members
            ],
            'notifiable_id' => $defense->id,
            'notifiable_type' => Defense::class,
            'read' => false
        ]);
    }

    private function getDefenseMessage(Defense $defense, $role = 'student')
    {
        $project = $defense->project;
        $juryNames = collect($defense->jury_members)->pluck('name')->join(', ');
        
        if ($role === 'supervisor') {
            return "La soutenance du projet '{$project->title}' de {$project->student->name} est programmée le {$defense->date->format('d/m/Y')} à {$defense->time->format('H:i')} en {$defense->location}. Jury : {$juryNames}";
        }
        
        return "Votre soutenance est programmée le {$defense->date->format('d/m/Y')} à {$defense->time->format('H:i')} en {$defense->location}. Jury : {$juryNames}";
    }
}