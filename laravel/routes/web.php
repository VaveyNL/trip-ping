<?php

use App\Http\Controllers\Auth\GitHubController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('trips.index'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('trips.index'))->name('dashboard');

    Route::resource('trips', TripController::class);

    Route::post('/trips/{trip}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::post('/trips/{trip}/participants', [TripController::class, 'addParticipant'])->name('trips.participants.add');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/github',          [GitHubController::class, 'redirect'])->name('auth.github');
Route::get('/auth/github/callback', [GitHubController::class, 'callback']);

require __DIR__.'/auth.php';

Route::get('/health', fn () => response()->json(['ok' => true]));
