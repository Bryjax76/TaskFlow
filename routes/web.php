<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\EmployeeController;
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
    Route::get('/tasks/create', [TaskController::class,'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class,'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskController::class,'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class,'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class,'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/status', [TaskController::class,'updateStatus'])->name('tasks.updateStatus');
    Route::post('/tasks/{task}/tags/quick', [TaskController::class,'quickAddTag'])->name('tasks.quickAddTag');
    Route::delete('/tasks/{task}/tags/{tag}', [TaskController::class,'removeTag'])->name('tasks.removeTag');
    Route::post('/tasks/{id}/restore', [TaskController::class,'restore'])->name('tasks.restore');
    Route::delete('/tasks/{id}/force-delete', [TaskController::class,'forceDelete'])->name('tasks.forceDelete');
    Route::get('/tasks/{task}', [TaskController::class,'show'])->name('tasks.show');

    Route::resource('projects', ProjectController::class);
    Route::resource('tags', TagController::class);
    Route::resource('employees', EmployeeController::class);
});

require __DIR__ . '/auth.php';
