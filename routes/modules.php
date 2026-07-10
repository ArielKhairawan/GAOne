<?php

use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('modules/{module}')->name('modules.')->group(function () {
    Route::get('/', [ModuleController::class, 'index'])->name('index');
    Route::get('/create', [ModuleController::class, 'create'])->name('create');
    Route::post('/', [ModuleController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ModuleController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ModuleController::class, 'update'])->name('update');
    Route::delete('/{id}', [ModuleController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/submit', [ModuleController::class, 'submit'])->name('submit');
});
