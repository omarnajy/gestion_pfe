<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'description',
        'type' // info, success, warning, danger
    ];

    /**
     * Get the project that owns the timeline event.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the timeline event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the icon class based on event type.
     */
    public function getIconClassAttribute()
    {
        switch ($this->type) {
            case 'success':
                return 'fa-check-circle text-success';
            case 'warning':
                return 'fa-exclamation-triangle text-warning';
            case 'danger':
                return 'fa-times-circle text-danger';
            case 'info':
            default:
                return 'fa-info-circle text-info';
        }
    }
}