<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

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
            $check = false;
            foreach (Auth::user()->tokens as $token) {
                if ($token->token == hash('sha256', substr($request->header('HasToken'), -40))) {
                    $check = true;
                }
            }
            // Jika ada maka pakai token yang lama
            if ($request->header('HasToken') != '' && $check) {
                $tokenResult = $request->header('HasToken');
            } else {
                $tokenResult = $user->createToken('authToken')->plainTextToken;
            }
            // Jika berhasil maka loginkan
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Authentication failed', 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken()->delete();

            return ResponseFormatter::success($token, 'Token revoked');
        } catch (Exception $error) {
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
                'phoneNumber' => 'no HP',
            );

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'phoneNumber' => ['required', 'max:15', 'phoneNumber'],
                    'address' => ['required', 'string', 'max:255'],
                    'password' => 'required|min:6',
                ],
                [
                    'email.unique' => 'Email sudah pernah digunakan sebelumnya, silahkan hubungi doltinukuid@gmail.com untuk bantuan',
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
                'phoneNumber' => $request->phoneNumber,
                'address' => $request->address,
                'is_active' => 1,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();
            // event(new Registered($user));

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User successfully registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Registration failed', 500);
        }
    }

    public function shoplogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Authentication failed', 422);
            }
            // $email = trim(preg_replace('/[\t\n\r\s]+/', '', ));
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

            // Mengecek credentials (login)
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Akun Anda tidak terdaftar'
                ], 'Authentication failed', 402);
            }

            if ($user->role_id != 3 && $user->role_id != 5) {
                return ResponseFormatter::error([
                    'message' => 'Anda bukan pemilik toko'
                ], 'Authentication failed', 405);
            }

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $shopId = Shop::where('owner_id', $user->id)->get()[0]->id;
            if (!$shopId)
                return ResponseFormatter::error([
                    'message' => 'Data toko tidak ada'
                ], 'Authentication failed', 404);

            if ($user->is_active == 0) {
                return ResponseFormatter::error([
                    'message' => 'Akun Anda tidak aktif'
                ], 'Authentication failed', 403);
            }

            $transactions = Transaction::select('shop_id', DB::raw('SUM(quantity) as sold,SUM(total) as income'))->whereHas('product', function ($q) {
                $q->where('deleted_at', NULL);
            })->where(['transactions.status' => 'DELIVERED', 'shop_id' => $shopId, 'transactions.deleted_at' => NULL])->groupBy('shop_id');

            $products = Product::select('shop_id', DB::raw('COUNT(shop_id) as total_products'))->where(['products.deleted_at' => NULL, 'shop_id' => $shopId])->groupBy('shop_id');

            $shop = Shop::leftJoinSub($transactions, 'transactions', function ($join) {
                $join->on('transactions.shop_id', '=', 'shops.id');
            })->leftJoinSub($products, 'products', function ($join) {
                $join->on('shops.id', '=', 'products.shop_id');
            })->get()->find($shopId);
            $check = false;
            foreach (Auth::user()->tokens as $token) {
                if ($token->token == hash('sha256', substr($request->header('HasToken'), -40))) {
                    $check = true;
                }
            }
            // Jika berhasil maka loginkan
            if ($request->header('HasToken') != '' && $check) {
                $tokenResult = $request->header('HasToken');
            } else {
                $tokenResult = $user->createToken('authToken')->plainTextToken;
            }
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'shop' => $shop,
                'user' => $user
            ], 'Authenticated with shop');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Authentication failed', 500);
        }
    }

    public function shopregister(Request $request)
    {
        try {
            $niceNames = array(
                'name' => 'nama lengkap',
                'phone' => 'no HP',
                'shopname' => 'nama toko',
                'shopaddress' => 'alamat toko',
                'shopdescription' => 'deskripsi toko',
            );

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string', 'max:60'],
                    'email' => ['required', 'email', 'max:50', 'unique:users'],
                    'phone' => ['required', 'max:15', 'phoneNumber'],
                    'password' => 'required|min:6',
                    'shopname' => ['required', 'string', 'max:50'],
                    'shopaddress' => ['required', 'string'],
                    'shopdescription' => ['required', 'string'],
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
                ], 'User & shop registration failed', 422);
            }

            if ($request->roleid != 3 && $request->roleid != "3") {
                return ResponseFormatter::error([
                    'message' => 'Anda bukan pemilik toko',
                ], 'User & shop registration failed', 403);
            }
            $email = trim(preg_replace('/[\t\n\r\s]+/', '', $request->email));
            User::create([
                'name' => $request->name,
                'role_id' => $request->roleid,
                'email' => $email,
                'phoneNumber' => $request->phone,
                'is_active' => 1,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            Shop::create([
                'owner_id' => $user->id,
                'name' => $request->shopname,
                'description' => $request->shopdescription,
                'address' => $request->shopaddress,
                // 'rating' => 0,
                'isValid' => 0,
                'isReject' => 0,
                'opening_hours' => '07:00',
                'closed_hours' => '17:00',
            ]);

            $shop = Shop::where('owner_id', $user->id)->get()[0];
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user = User::find($shop->owner_id);
            $user->notify(new \App\Notifications\Appeal($user, $shop->name, $user->name));
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'shop' => $shop,
                'user' => $user
            ], 'User & shop successfully registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'User & shop registration failed', 500);
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
                    'phone' => ['required', 'max:15', 'phoneNumber'],
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
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Send link reset password failed', 500);
        }
    }
}
