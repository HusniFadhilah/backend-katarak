<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('home.index');
    }

    public function redirect()
    {
        $user = Auth::user();
        if ($user) {
            if ($user->is_active && $user->is_verified)
                return redirect()->route('dashboard');
            return view('auth.check-status', compact('user'));
        }
    }

    public function privacy()
    {
        return view('home.privacy');
    }
}
