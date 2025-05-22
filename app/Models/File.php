<?php
// app/Models/File.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'filename',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'type',
        'version',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDownloadUrlAttribute()
    {
        return route('files.download', $this->id);
    }
}