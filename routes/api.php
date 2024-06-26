<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\TaskOwnerMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});

Route::controller(TaskController::class)
    ->middleware('auth:sanctum')
    ->prefix('tasks')
    ->group(function () {
        Route::get('/', 'index')->name('tasks.index');
        Route::get('/deleted', 'getDeleted')->middleware(AdminMiddleware::class)->name('tasks.deleted');
        Route::get('/{task}', 'show')->middleware(TaskOwnerMiddleware::class)->name('tasks.show');
        Route::post('/', 'store')->name('tasks.store');
        Route::put('/{task}', 'update')->middleware(TaskOwnerMiddleware::class)->name('tasks.update');
        Route::delete('/{task}', 'destroy')->middleware(TaskOwnerMiddleware::class)->name('tasks.destroy');
    });
