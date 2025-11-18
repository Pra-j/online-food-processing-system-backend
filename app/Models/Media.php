<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'file_name',
        'mime_type',
        'size',
        'disk'
    ];

    // Convenience accessor for public URL
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }
}
