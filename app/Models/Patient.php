<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'created_by', 'modificated_by', 'name', 'ktp', 'gender', 'birth_date', 'birth_place', 'address'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function eyeImages()
    {
        return $this->hasMany(EyeImage::class);
    }

    public function eyeExaminations()
    {
        return $this->hasMany(EyeExamination::class);
    }
}
