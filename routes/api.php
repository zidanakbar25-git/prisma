
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\TaskController;

Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('api.user');

    // =========================
    // KEGIATAN
    // =========================

    Route::get('/activities', [ActivityController::class, 'index'])
        ->name('api.activities.index');

    Route::get('/activities/{activity}', [ActivityController::class, 'show'])
        ->name('api.activities.show');

    Route::post('/activities', [ActivityController::class, 'store'])
        ->name('api.activities.store');

    Route::put('/activities/{activity}', [ActivityController::class, 'update'])
        ->name('api.activities.update');

    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])
        ->name('api.activities.destroy');

    // =========================
    // TO-DO
    // =========================

    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('api.tasks.index');

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('api.tasks.show');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('api.tasks.store');

    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->name('api.tasks.update');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('api.tasks.destroy');

    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('api.tasks.update-status');
});
