<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function getKtpAttribute()
    {
        return decrypt($this->attributes['ktp']);
    }

    public function getKtpOriginal()
    {
        return $this->attributes['ktp'];
    }
}
