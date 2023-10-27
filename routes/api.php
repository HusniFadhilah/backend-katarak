<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{AuthController, EyeDisorderController, JobController, PastMedicalController, UserController};

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

Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('changepassword', [UserController::class, 'changePassword']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/job', [JobController::class, 'index'])->name('job');
Route::get('/past-medical', [PastMedicalController::class, 'index'])->name('past-medical');
Route::get('/eye-disorder', [EyeDisorderController::class, 'index'])->name('eye-disorder');
