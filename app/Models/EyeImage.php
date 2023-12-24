<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EyeImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'eye_examination_id', 'patient_id', 'kader_id', 'image_path'
    ];

    public function eyeExamination()
    {
        return $this->belongsTo(EyeExamination::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function kader()
    {
        return $this->belongsTo(User::class, 'kader_id', 'id');
    }

    public function getImagePathAttribute()
    {
        return url('') . Storage::url($this->attributes['image_path']);
    }

    public function getImagePathOriginal()
    {
        return $this->attributes['image_path'];
    }

    public function deleteFile()
    {
        if (file_exists(storage_path('app/public/' . $this->getImagePathOriginal())))
            unlink(storage_path('app/public/' . $this->getImagePathOriginal()));
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($obj) {
            $obj->deleteFile();
        });
    }
}
