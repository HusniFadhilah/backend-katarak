<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\PasswordValidationRules;

class ProfileController extends Controller
{
    use PasswordValidationRules;

    public function edit()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];
        if (($request->password)) {
            $rules['password'] = $this->passwordRules(false);
        }

        $request->validate($rules);
        $attr = $request->all();
        $attr['password'] =  isset($request->password) ? Hash::make(request('password')) : $user->password;
        $user->update($attr);

        Fungsi::sweetalert('Profile berhasil diupdate', 'success', 'Berhasil!');
        return back();
    }
}
