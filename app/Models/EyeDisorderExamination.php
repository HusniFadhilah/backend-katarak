<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EyeDisorderExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'eye_examination_id', 'eye_disorder_id'
    ];

    public function eyeExamination()
    {
        return $this->belongsTo(EyeExamination::class);
    }

    public function eyeDisorder()
    {
        return $this->belongsTo(EyeDisorder::class);
    }
}
