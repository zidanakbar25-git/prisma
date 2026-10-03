<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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


    // user


    

    Route::get('/pengguna', [UserController::class, 'index'])
    ->middleware('role:kabag')
    ->name('users.index');

Route::get('/pengguna/tambah', [UserController::class, 'create'])
    ->middleware('role:kabag')
    ->name('users.create');

Route::post('/pengguna', [UserController::class, 'store'])
    ->middleware('role:kabag')
    ->name('users.store');

Route::get('/pengguna/{user}/edit', [UserController::class, 'edit'])
    ->middleware('role:kabag')
    ->name('users.edit');

Route::put('/pengguna/{user}', [UserController::class, 'update'])
    ->middleware('role:kabag')
    ->name('users.update');

Route::get('/pengguna/{user}/reset-password', function (\App\Models\User $user) {
    abort_unless(
        auth()->user()->role === 'kabag',
        403
    );

    return view(
        'users.reset-password',
        compact('user')
    );
})
    ->name('users.reset-password');

Route::put('/pengguna/{user}/reset-password', [UserController::class, 'resetPassword'])
    ->middleware('role:kabag')
    ->name('users.reset-password.store');

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

    Route::get('/to-do', [TaskController::class, 'index'])
    ->name('tasks.index');

Route::get('/to-do/tambah', [TaskController::class, 'create'])
    ->middleware('role:kabag')
    ->name('tasks.create');

Route::post('/to-do', [TaskController::class, 'store'])
    ->middleware('role:kabag')
    ->name('tasks.store');

Route::get('/to-do/{task}/edit', [TaskController::class, 'edit'])
    ->middleware('role:kabag')
    ->name('tasks.edit');

Route::put('/to-do/{task}', [TaskController::class, 'update'])
    ->middleware('role:kabag')
    ->name('tasks.update');

Route::delete('/to-do/{task}', [TaskController::class, 'destroy'])
    ->middleware('role:kabag')
    ->name('tasks.destroy');

Route::put('/to-do/{task}/status', [TaskController::class, 'updateStatus'])
    ->name('tasks.update-status');
});