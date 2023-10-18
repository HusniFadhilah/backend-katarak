<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EyeExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'kader_id', 'doctor_id', 'examination_date_time', 'right_eye_vision', 'left_eye_vision', 'eye_disorder_id', 'past_medical_id', 'other_eye_disorder', 'other_past_medical', 'latitude', 'longitude', 'status', 'evaluation_description'
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

    public function eyeDisorder()
    {
        return $this->belongsTo(EyeDisorder::class, 'kader_id', 'id');
    }

    public function pastMedical()
    {
        return $this->belongsTo(PastMedical::class, 'kader_id', 'id');
    }
}
