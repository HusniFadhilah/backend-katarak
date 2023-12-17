<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastMedicalExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'eye_examination_id', 'past_medical_id'
    ];

    public function eyeExamination()
    {
        return $this->belongsTo(EyeExamination::class);
    }

    public function pastMedical()
    {
        return $this->belongsTo(PastMedical::class);
    }
}
