<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EyeExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'kader_id', 'doctor_id', 'examination_date_time', 'verification_date_time', 'right_eye_vision', 'left_eye_vision', 'other_eye_disorder', 'other_past_medical', 'latitude', 'longitude', 'formatted_location', 'status', 'evaluation_description'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function kader()
    {
        return $this->belongsTo(User::class, 'kader_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function eyeDisorders()
    {
        return $this->hasManyThrough(EyeDisorder::class, EyeDisorderExamination::class, 'eye_examination_id', 'id', 'id', 'eye_disorder_id');
    }

    public function pastMedicals()
    {
        return $this->hasManyThrough(PastMedical::class, PastMedicalExamination::class, 'eye_examination_id', 'id', 'id', 'past_medical_id');
    }

    public function eyeImages()
    {
        return $this->hasMany(EyeImage::class);
    }

    public function eyeDisorderExaminations()
    {
        return $this->hasMany(EyeDisorderExamination::class);
    }

    public function pastMedicalExaminations()
    {
        return $this->hasMany(PastMedicalExamination::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($eyeExamination) {
            foreach ($eyeExamination->eyeImages()->get() as $eyeImage) {
                $eyeImage->deleteFile();
                $eyeImage->delete();
            }
            $eyeExamination->eyeDisorderExaminations()->delete();
            $eyeExamination->pastMedicalExaminations()->delete();
            $eyeExamination->kader->refreshCounts();
            $eyeExamination->doctor->refreshCounts();
        });
    }
}
