<?php

use App\Http\Controllers\AuthController;
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
| Dashboard Kabag
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/kabag', function () {
    return view('dashboard.kabag');
})
    ->middleware(['auth', 'role:kabag'])
    ->name('dashboard.kabag');


/*
|--------------------------------------------------------------------------
| Dashboard Staff
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/staff', function () {
    return view('dashboard.staff');
})
    ->middleware(['auth', 'role:staff'])
    ->name('dashboard.staff');


/*
|--------------------------------------------------------------------------
| Dashboard Intern
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/intern', function () {
    return view('dashboard.intern');
})
    ->middleware(['auth', 'role:intern'])
    ->name('dashboard.intern');