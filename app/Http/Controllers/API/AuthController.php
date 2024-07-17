<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Support\Facades\{Password, Validator};

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Authentication failed', 422);
            }
            // $email = trim(preg_replace('/[\t\n\r\s]+/', ' ', ));
            // Jika hash tidak sesuai maka beri error
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'Email Anda tidak terdaftar'
                ], 'Authentication failed', 404);
            }
            if (!Hash::check($request->password, $user->password, [])) {
                return ResponseFormatter::error([
                    'message' => 'Password Anda salah'
                ], 'Authentication failed', 404);
            }

            //Mengecek credentials (login)
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Akun tidak terdaftar'
                ], 'Authentication failed', 404);
            }
            if ($user->role_id == 1) {
                return ResponseFormatter::error([
                    'message' => 'Anda adalah admin, silahkan login di web'
                ], 'Authentication failed', 401);
            }
            $token = $request->bearerToken();
            if ($token) {
                if (str_contains($token, '|')) {
                    $subToken = explode('|', $token)[1];
                    // $subToken = substr($token, -48);
                    $whereToken = hash('sha256', $subToken);
                } else {
                    $whereToken = $token;
                }
                $validToken = PersonalAccessToken::where('tokenable_id', $user->id)->where('token', $whereToken)->first();
                $tokenResult = $validToken ? $token : $this->getTokenResult($user, $request);
            } else {
                $tokenResult = $this->getTokenResult($user, $request);
            }
            // Jika berhasil maka loginkan
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Authentication failed', 500);
        }
    }

    private function getTokenResult($user, $request)
    {
        // $user->tokens()->delete();
        $token = $user->createToken('authToken');
        $personalAccessToken = PersonalAccessToken::find($token->accessToken->id);
        if ($request->fcm_token) {
            $personalAccessToken->fcm_token = $request->fcm_token;
            $personalAccessToken->save();
        }
        $tokenResult = $token->plainTextToken;
        return $tokenResult;
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken()->delete();

            return ResponseFormatter::success($token, 'Token revoked');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Logout failed', 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $niceNames = array(
                'name' => 'nama lengkap',
                'phone_number' => 'no HP',
                'role_id' => 'role',
            );

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'phone_number' => ['required', 'max:15', 'phone_number'],
                    'role_id' => ['required'],
                    'password' => 'required|min:6',
                ],
                [
                    'email.unique' => 'Email sudah pernah digunakan sebelumnya, silahkan hubungi admin untuk bantuan',
                ],
                $niceNames
            );

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Registration failed', 422);
            }
            $email = trim(preg_replace('/[\t\n\r\s]+/', '', $request->email));
            User::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
                'email' => $email,
                'phone_number' => $request->phone_number,
                'is_active' => 1,
                'is_verified' => 0,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();
            // event(new Registered($user));

            $token = $user->createToken('authToken');
            $personalAccessToken = PersonalAccessToken::find($token->accessToken->id);
            if ($request->fcm_token) {
                $personalAccessToken->fcm_token = $request->fcm_token;
                $personalAccessToken->save();
            }
            $tokenResult = $token->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User successfully registered');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Registration failed', 500);
        }
    }

    public function checkUser(Request $request)
    {
        try {
            $niceNames = array(
                'name' => 'nama lengkap',
                'phone' => 'no HP',
            );

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string', 'max:60'],
                    'email' => ['required', 'email', 'max:50', 'unique:users'],
                    'phone' => ['required', 'max:15', 'phone_number'],
                    'password' => 'required|min:4',
                ],
                [
                    'email.unique' => 'Email sudah pernah digunakan sebelumnya, silahkan hubungi doltinukuid@gmail.com atau pengurus UKM Gerai Kopimi untuk bantuan',
                ],
                $niceNames
            );

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Check user failed', 422);
            }

            return ResponseFormatter::success([], 'Check user success');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Check user failed', 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Send link reset password failed', 422);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status == Password::RESET_LINK_SENT) {
                return ResponseFormatter::success(['message' => 'Link reset password berhasil dikirim. Silahkan cek email Anda di ' . $request->email . ' untuk melakukan reset password'], 'Password Reset Link Has Been Sent');
            } else {
                return ResponseFormatter::error(['message' => 'Link reset password gagal dikirim', 'error' => ['email' => ['Silahkan coba dalam beberapa waktu lagi']]], 'Send link reset password failed', 403);
            }
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Send link reset password failed', 500);
        }
    }
}
