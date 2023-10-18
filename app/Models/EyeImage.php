<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
