<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'name',
        'type',
        'path',
        'size',
        'extension',
        'uploaded_by',
    ];

/**
     * Get the user who uploaded the document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the project that owns the document.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

 /**
     * Get the formatted size of the document.
     */
    public function getSizeFormattedAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get the icon class based on file type.
     */
    public function getIconClassAttribute()
    {
        $extension = $this->extension;
        
        if (in_array($extension, ['pdf'])) {
            return 'fa-file-pdf';
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return 'fa-file-word';
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return 'fa-file-excel';
        } elseif (in_array($extension, ['ppt', 'pptx'])) {
            return 'fa-file-powerpoint';
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'fa-file-image';
        } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
            return 'fa-file-archive';
        } else {
            return 'fa-file';
        }
    }
}