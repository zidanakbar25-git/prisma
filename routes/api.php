<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    Route::get('/user', function (Request $request) {
        return $request->user();
    });


   /*
|--------------------------------------------------------------------------
| Kalender Kegiatan
|--------------------------------------------------------------------------
*/

Route::get('/activities', [ActivityController::class, 'index'])
    ->name('api.activities.index');

Route::get('/activities/{activity}', [ActivityController::class, 'show'])
    ->name('api.activities.show');

Route::post('/activities', [ActivityController::class, 'store'])
    ->name('api.activities.store');

    Route::put('/activities/{activity}', [ActivityController::class, 'update'])
    ->name('api.activities.update');
});