<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data', // JSON pour stocker les données de la soutenance
        'notifiable_id',
        'notifiable_type',
        'read',
        'scheduled_for' // Pour programmer l'envoi
    ];

    protected $casts = [
        'read' => 'boolean',
        'data' => 'array',
        'scheduled_for' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        $this->read = true;
        $this->save();
    }
}