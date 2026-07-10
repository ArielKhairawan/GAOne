<?php

use App\Http\Controllers\Vehicle\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('vehicle')->name('vehicle.')->group(function () {
    Route::get('/', [VehicleController::class, 'index'])->name('index')->middleware('permission:vehicle.view');
    Route::get('/create', [VehicleController::class, 'create'])->name('create')->middleware('permission:vehicle.create');
    Route::post('/', [VehicleController::class, 'store'])->name('store')->middleware('permission:vehicle.create');
    Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit')->middleware('permission:vehicle.edit');
    Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update')->middleware('permission:vehicle.edit');
    Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy')->middleware('permission:vehicle.delete');
});
