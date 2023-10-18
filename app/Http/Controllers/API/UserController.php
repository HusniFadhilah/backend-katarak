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

    public function updatePhoto(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|image|max:2048'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Update photo profile failed', 422);
            }

            if ($request->file('file')) {

                $file = $request->file->store('assets/user', 'public');

                //store your file into database
                $user = Auth::user();
                $user->profile_photo_path = $file;
                $user->update();

                return ResponseFormatter::success([$file], 'File successfully updated');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Update photo profile failed', 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            $niceNames = array(
                'oldpassword' => 'password lama',
                'newpassword' => 'password baru',
                'confpassword' => 'konfirmasi password baru',
            );

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'Akun Anda tidak terdaftar'
                ], 'Change password failed', 403);
            }

            $validator = Validator::make($request->all(), [
                'oldpassword' => 'required|current_password:' . $user->id,
                'newpassword' => 'required|min:4|different_password:' . $user->id,
                'confpassword' => 'required|same:newpassword'
            ], [], $niceNames);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Change password failed', 422);
            }

            $user->password = Hash::make($request->newpassword);
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
