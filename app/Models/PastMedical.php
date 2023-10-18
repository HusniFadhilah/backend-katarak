<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastMedical extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function eyeExaminations()
    {
        return $this->hasMany(EyeExamination::class, 'kader_id', 'id');
    }
}
