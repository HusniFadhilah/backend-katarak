<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Support\Facades\{Hash, Auth, Validator};

class UserController extends Controller
{
    use PasswordValidationRules;

    public function fetch(Request $request)
    {
        return ResponseFormatter::success(
            $request->user(),
            'Data profile user berhasil diambil'
        );
    }

    public function updateProfile(Request $request)
    {
        try {
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:60'],
                'phoneNumber' => ['required', 'max:15', 'phoneNumber'],
                'address' => 'required'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Update profile failed', 422);
            }

            $user = Auth::user();
            $user->update($data);

            return ResponseFormatter::success($user, 'Profile have been updated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Update profile failed', 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            $niceNames = array(
                'old_password' => 'password lama',
                'new_password' => 'password baru',
                'conf_password' => 'konfirmasi password baru',
            );

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'Akun Anda tidak terdaftar'
                ], 'Change password failed', 403);
            }

            $validator = Validator::make($request->all(), [
                'old_password' => 'required|current_password:' . $user->id,
                'new_password' => 'required|min:4|different_password:' . $user->id,
                'conf_password' => 'required|same:new_password'
            ], [], $niceNames);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Change password failed', 422);
            }

            $user->password = Hash::make($request->new_password);
            $user->update();

            return ResponseFormatter::success([
                'user' => $user
            ], 'Password have been updated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Change password failed', 500);
        }
    }
}
