<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'alias'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($role) {
            $role->users()->delete();
        });
    }
}
