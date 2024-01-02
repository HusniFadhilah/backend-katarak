<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{AuthController, EyeDisorderController, EyeImageController, EyeExaminationController, JobController, PastMedicalController, PatientController, UserController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::name('api.')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('user', [UserController::class, 'updateProfile']);
        Route::get('user', [UserController::class, 'fetch']);
        Route::post('changepassword', [UserController::class, 'changePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('/job', [JobController::class, 'index'])->name('job');
        Route::get('/past-medical', [PastMedicalController::class, 'index'])->name('past-medical');
        Route::get('/eye-disorder', [EyeDisorderController::class, 'index'])->name('eye-disorder');
        Route::get('/eye-examination', [EyeExaminationController::class, 'index'])->name('eye-examination');
        Route::get('/patient', [PatientController::class, 'index'])->name('patient');
        Route::post('/patient/store', [PatientController::class, 'store'])->name('patient.store');

        Route::post('/eye-examination/store', [EyeExaminationController::class, 'store'])->name('eye-examination.store');
        Route::delete('/eye-examination/{id}/destroy', [EyeExaminationController::class, 'destroy'])->name('eye-examination.destroy');
        Route::post('/eye-examination/image/{id}/store', [EyeExaminationController::class, 'uploadImage'])->name('eye-examination.upload-image');
        Route::post('/eye-examination/image/multiple/{id}/store', [EyeImageController::class, 'uploadMultipleImage']);
    });
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});
