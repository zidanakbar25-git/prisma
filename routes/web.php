<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard
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
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Kalender Kegiatan
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/kalender-kegiatan',
        [ActivityController::class, 'index']
    )
        ->name('activities.index');

    Route::get(
        '/kalender-kegiatan/tambah',
        [ActivityController::class, 'create']
    )
        ->middleware('role:kabag')
        ->name('activities.create');

    Route::post(
        '/kalender-kegiatan',
        [ActivityController::class, 'store']
    )
        ->middleware('role:kabag')
        ->name('activities.store');

    Route::get(
        '/kalender-kegiatan/export',
        [ActivityController::class, 'exportForm']
    )
        ->name('activities.export.form');

    Route::get(
        '/kalender-kegiatan/export/pdf',
        [ActivityController::class, 'exportPdf']
    )
        ->name('activities.export.pdf');

    Route::get(
        '/kalender-kegiatan/{activity}',
        [ActivityController::class, 'show']
    )
        ->name('activities.show');

    Route::get(
        '/kalender-kegiatan/{activity}/edit',
        [ActivityController::class, 'edit']
    )
        ->middleware('role:kabag')
        ->name('activities.edit');

    Route::put(
        '/kalender-kegiatan/{activity}',
        [ActivityController::class, 'update']
    )
        ->middleware('role:kabag')
        ->name('activities.update');

    Route::delete(
        '/kalender-kegiatan/{activity}',
        [ActivityController::class, 'destroy']
    )
        ->middleware('role:kabag')
        ->name('activities.destroy');


    /*
    |--------------------------------------------------------------------------
    | To-Do
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/to-do',
        [TaskController::class, 'index']
    )
        ->name('tasks.index');

    Route::get(
        '/to-do/tambah',
        [TaskController::class, 'create']
    )
        ->middleware('role:kabag')
        ->name('tasks.create');

    Route::post(
        '/to-do',
        [TaskController::class, 'store']
    )
        ->middleware('role:kabag')
        ->name('tasks.store');

        Route::put('/to-do/{task}/status', [TaskController::class, 'updateStatus'])
    ->name('tasks.update-status');
});