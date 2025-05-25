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
        'original_name',
        'type',
        'path',
        'size',
        'mime_type',
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
    public function getFormattedSizeAttribute()
    {
        if (!$this->size) {
            return '0 B';
        }

        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the display name for the document.
     * Si le champ 'name' est vide, utilise le nom original du fichier.
     */
    public function getDisplayNameAttribute()
    {
        return $this->attributes['name'] ?: ($this->original_name ?: 'Document sans nom');
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