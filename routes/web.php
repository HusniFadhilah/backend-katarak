<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\{AdminController, DependentDropdownController, EyeDisorderController, EyeExaminationController, JobController, HomeController, PastMedicalController, PatientController, ProfileController, UserController};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('redirect', [HomeController::class, 'redirect']);
Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('home');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::post('/user/bulkdestroy', [App\Http\Controllers\UserController::class, 'bulkDestroy'])->name('user.bulkdestroy');
    Route::delete('/user/{user}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    Route::resource('/user', UserController::class, [
        'names' => [
            'index' => 'user',
            'create' => 'user.create',
            'destroy' => 'user.delete'
        ]
    ]);

    Route::post('/patient/bulkdestroy', [App\Http\Controllers\PatientController::class, 'bulkDestroy'])->name('patient.bulkdestroy');
    Route::delete('/patient/{patient}/destroy', [PatientController::class, 'destroy'])->name('patient.destroy');
    Route::resource('/patient', PatientController::class, [
        'names' => [
            'index' => 'patient',
            'create' => 'patient.create',
            'destroy' => 'patient.delete'
        ]
    ]);

    Route::post('/job/bulkdestroy', [App\Http\Controllers\JobController::class, 'bulkDestroy'])->name('job.bulkdestroy');
    Route::delete('/job/{job}/destroy', [JobController::class, 'destroy'])->name('job.destroy');
    Route::resource('/job', JobController::class, [
        'names' => [
            'index' => 'job',
            'create' => 'job.create',
            'destroy' => 'job.delete'
        ]
    ]);

    Route::post('/past-medical/bulkdestroy', [App\Http\Controllers\PastMedicalController::class, 'bulkDestroy'])->name('past-medical.bulkdestroy');
    Route::delete('/past-medical/{pastMedical}/destroy', [PastMedicalController::class, 'destroy'])->name('past-medical.destroy');
    Route::resource('/past-medical', PastMedicalController::class, [
        'names' => [
            'index' => 'past-medical',
            'create' => 'past-medical.create',
            'destroy' => 'past-medical.delete'
        ]
    ]);

    Route::post('/eye-disorder/bulkdestroy', [App\Http\Controllers\EyeDisorderController::class, 'bulkDestroy'])->name('eye-disorder.bulkdestroy');
    Route::delete('/eye-disorder/{eyeDisorder}/destroy', [EyeDisorderController::class, 'destroy'])->name('eye-disorder.destroy');
    Route::resource('/eye-disorder', EyeDisorderController::class, [
        'names' => [
            'index' => 'eye-disorder',
            'create' => 'eye-disorder.create',
            'destroy' => 'eye-disorder.delete'
        ]
    ]);

    Route::post('/eye-examination/bulkdestroy', [App\Http\Controllers\EyeExaminationController::class, 'bulkDestroy'])->name('eye-examination.bulkdestroy');
    Route::delete('/eye-examination/{eyeExamination}/destroy', [EyeExaminationController::class, 'destroy'])->name('eye-examination.destroy');
    Route::resource('/eye-examination', EyeExaminationController::class, [
        'names' => [
            'index' => 'eye-examination',
            'create' => 'eye-examination.create',
            'destroy' => 'eye-examination.delete'
        ]
    ]);

    Route::get('/profile/{user}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update/{user}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('roles', [DependentDropdownController::class, 'getRoles'])->name('getroles');
    Route::get('jobs', [DependentDropdownController::class, 'getJobs'])->name('getjobs');
    Route::get('patients', [DependentDropdownController::class, 'getPatients'])->name('getpatients');
    Route::get('doctors', [DependentDropdownController::class, 'getDoctors'])->name('getdoctors');
    Route::get('kaders', [DependentDropdownController::class, 'getKaders'])->name('getkaders');
    Route::get('eye-disorders', [DependentDropdownController::class, 'getEyeDisorders'])->name('geteyedisorders');
    Route::get('past-medicals', [DependentDropdownController::class, 'getPastMedicals'])->name('getpastmedicals');
    Route::post('getshowktp', [DependentDropdownController::class, 'getShowKTP'])->name('getshowktp');
});

Route::get('test', [EyeExaminationController::class, 'test']);

Route::get('clearcache', function () {
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    Illuminate\Support\Facades\Artisan::call('route:clear');
    Illuminate\Support\Facades\Artisan::call('view:clear');
    Illuminate\Support\Facades\Artisan::call('config:clear');
    Illuminate\Support\Facades\Artisan::call('config:cache');
});

Route::get('composerinstall', function () {
    shell_exec('composer install');
});

Route::get('migrateseed', function () {
    Artisan::call('migrate:fresh');
    Artisan::call(
        'db:seed',
        array(
            '--force' => true
        )
    );
});

Route::get('storagelink', function () {
    Artisan::call(
        'storage:link'
    );
});
