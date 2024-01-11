<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $niceNames = array(
            'name' => 'nama lengkap',
            'phone_number' => 'no HP',
            'role_id' => 'role',
        );

        Validator::make(
            $input,
            [
                'name' => ['required', 'string', 'max:255'],
                'role_id' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
                'phone_number' => ['required', 'max:17'],
            ],
            [
                'email.unique' => 'Email sudah pernah digunakan sebelumnya',
            ],
            $niceNames
        )->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role_id' => $input['role_id'],
            'phone_number' => $input['phone_number'],
            'password' => Hash::make($input['password']),
            'is_active' => 1,
            'is_verified' => 0,
        ]);
    }
}
