
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\BackupController;

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

    // =========================
    // USER MANAGEMENT
    // =========================

    Route::get('/users', [UserController::class, 'index'])
        ->name('api.users.index');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('api.users.show');

    Route::post('/users', [UserController::class, 'store'])
        ->name('api.users.store');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('api.users.update');

    Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('api.users.reset-password');

    Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('api.users.toggle-status');


    // =========================
    // AUDIT LOG
    // =========================

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('api.audit-logs.index');

    // =========================
    // BACKUP DATABASE
    // =========================

    Route::get('/backup/database', [BackupController::class, 'download'])
        ->name('api.backup.download');


    //logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('api.logout');

});
