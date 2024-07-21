<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id', 'created_by', 'modificated_by', 'name', 'email', 'password', 'phone_number', 'is_verified', 'is_active', 'count_verify', 'count_examination', 'count_examination_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role)
    {
        return strtolower($this->role->name) == strtolower($role);
    }

    public function eyeImages()
    {
        return $this->hasMany(EyeImage::class, 'kader_id', 'id');
    }

    public function eyeExaminationKaders()
    {
        return $this->hasMany(EyeExamination::class, 'kader_id', 'id');
    }

    public function eyeExaminationDoctors()
    {
        return $this->hasMany(EyeExamination::class, 'doctor_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token, $this->name));
    }

    public function personalAccessTokens()
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id', 'id');
    }

    public function refreshCounts()
    {
        $this->count_verify = EyeExamination::where('doctor_id', $this->id)
            ->where('status', '!=', 'wait')
            ->count();

        $this->count_examination = EyeExamination::where('kader_id', $this->id)
            ->count();

        $this->count_examination_verified = EyeExamination::where('kader_id', $this->id)
            ->where('status', '!=', 'wait')
            ->count();
        $this->save();
    }
}
