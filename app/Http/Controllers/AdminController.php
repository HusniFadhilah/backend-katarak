<?php

namespace App\Http\Controllers;

use App\Models\{EyeDisorder, EyeExamination, Job, PastMedical, Patient, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $authUser = Auth::user();
        $countData = [
            ['id' => 'users', 'title' => 'USER', 'bg' => 'success', 'count' => User::count(), 'icon' => 'fa fa-users', 'href' => route('user')],
            ['id' => 'patients', 'title' => 'PASIEN', 'bg' => 'dark', 'count' => Patient::count(), 'icon' => 'icon-people', 'href' => route('patient')],
            ['id' => 'jobs', 'title' => 'PEKERJAAN', 'bg' => 'warning', 'count' => Job::count(), 'icon' => 'icon-work', 'href' => route('job')],
            ['id' => 'eyeDisorders', 'title' => 'KELUHAN MATA', 'bg' => 'danger', 'count' => EyeDisorder::count(), 'icon' => 'icon-eye', 'href' => route('eye-disorder')],
            ['id' => 'pastMedicals', 'title' => 'RIWAYAT PENYAKIT', 'bg' => 'info', 'count' => PastMedical::count(), 'icon' => 'ion-ios-medical', 'href' => route('past-medical')],
            ['id' => 'eyeExamination', 'title' => 'PEMERIKSAAN MATA', 'bg' => 'primary', 'count' => EyeExamination::count(), 'icon' => 'fa fa-user-md', 'href' => route('eye-examination')],
        ];
        return view('roles.dashboard.index', compact('authUser', 'countData'));
    }
}
