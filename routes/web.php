<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/kabag', function () {
    return view('dashboard.kabag');
})
    ->middleware(['auth', 'role:kabag'])
    ->name('dashboard.kabag');

Route::get('/dashboard/staff', function () {
    return view('dashboard.staff');
})
    ->middleware(['auth', 'role:staff'])
    ->name('dashboard.staff');

Route::get('/dashboard/intern', function () {
    return view('dashboard.intern');
})
    ->middleware(['auth', 'role:intern'])
    ->name('dashboard.intern');


/*
|--------------------------------------------------------------------------
| Kalender Kegiatan
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Daftar kegiatan
    Route::get('/kalender-kegiatan', [ActivityController::class, 'index'])
        ->name('activities.index');

    // Form tambah kegiatan - Kabag
    Route::get('/kalender-kegiatan/tambah', [ActivityController::class, 'create'])
        ->middleware('role:kabag')
        ->name('activities.create');

    // Simpan kegiatan - Kabag
    Route::post('/kalender-kegiatan', [ActivityController::class, 'store'])
        ->middleware('role:kabag')
        ->name('activities.store');

    // Detail kegiatan
    Route::get('/kalender-kegiatan/{activity}', [ActivityController::class, 'show'])
        ->name('activities.show');

    // Form edit kegiatan - Kabag
    Route::get('/kalender-kegiatan/{activity}/edit', [ActivityController::class, 'edit'])
        ->middleware('role:kabag')
        ->name('activities.edit');

    // Update kegiatan - Kabag
    Route::put('/kalender-kegiatan/{activity}', [ActivityController::class, 'update'])
        ->middleware('role:kabag')
        ->name('activities.update');

    // Hapus kegiatan - Kabag
    Route::delete('/kalender-kegiatan/{activity}', [ActivityController::class, 'destroy'])
        ->middleware('role:kabag')
        ->name('activities.destroy');
});