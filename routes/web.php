<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tasks', [TaskController::class,'index'])->name('tasks.index');
    Route::get('/tasks/add', [TaskController::class,'create'])->name('tasks.create');
    Route::post('/tasks/store', [TaskController::class,'store'])->name('tasks.store');
    Route::get('/tasks/edit/{id}', [TaskController::class,'edit'])->name('tasks.edit');
    Route::post('/tasks/destroy/{id}', [TaskController::class,'destroy'])->name('tasks.destroy');
});

require __DIR__ . '/auth.php';
