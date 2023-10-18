<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $authUser = Auth::user();
        return view('roles.dashboard.index', compact('authUser'));
    }
}
