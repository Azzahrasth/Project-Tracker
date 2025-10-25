<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Tampilan utama (Single Page Application)
Route::get('/', [ProjectController::class, 'index'])->name('projects.index');

// CRUD Project (Menggunakan Resource-like structure untuk AJAX)
Route::prefix('projects')->name('projects.')->group(function () {
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
});

// CRUD Task (Menggunakan Resource-like structure untuk AJAX)
Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::post('/', [TaskController::class, 'store'])->name('store');
    Route::put('/{task}', [TaskController::class, 'update'])->name('update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
});
