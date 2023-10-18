<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AdminController, DependentDropdownController, HomeController, ProfileController, UserController};
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

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

    Route::get('/profile/{user}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update/{user}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('roles', [DependentDropdownController::class, 'getRoles'])->name('getroles');
});


// Route::get('clearcache', function () {
//     Illuminate\Support\Facades\Artisan::call('cache:clear');
//     Illuminate\Support\Facades\Artisan::call('route:clear');
//     Illuminate\Support\Facades\Artisan::call('view:clear');
//     Illuminate\Support\Facades\Artisan::call('config:clear');
//     Illuminate\Support\Facades\Artisan::call('config:cache');
// });

// Route::get('composerinstall', function () {
//     shell_exec('composer install');
// });

// Route::get('migrateseed', function () {
//     Illuminate\Support\Facades\Artisan::call('migrate:fresh');
//     Artisan::call(
//         'db:seed',
//         array(
//             '--force' => true
//         )
//     );
// });

// Route::get('storagelink', function () {
//     Artisan::call(
//         'storage:link'
//     );
// });
