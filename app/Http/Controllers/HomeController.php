<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('guest.index');
    }

    public function redirect()
    {
        $user = Auth::user();
        if ($user) {
            return redirect()->route('dashboard');
        }
    }
}
