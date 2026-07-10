<?php

use App\Http\Controllers\Fuel\FuelLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('fuel')->name('fuel.')->group(function () {
    Route::get('/', [FuelLogController::class, 'index'])->name('index')->middleware('permission:fuel.view');
    Route::get('/create', [FuelLogController::class, 'create'])->name('create')->middleware('permission:fuel.create');
    Route::post('/', [FuelLogController::class, 'store'])->name('store')->middleware('permission:fuel.create');
    Route::get('/export/pdf', [FuelLogController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:fuel.export');
    Route::get('/export/excel', [FuelLogController::class, 'exportExcel'])->name('export.excel')->middleware('permission:fuel.export');
    Route::get('/{fuelLog}/edit', [FuelLogController::class, 'edit'])->name('edit')->middleware('permission:fuel.edit');
    Route::put('/{fuelLog}', [FuelLogController::class, 'update'])->name('update')->middleware('permission:fuel.edit');
    Route::delete('/{fuelLog}', [FuelLogController::class, 'destroy'])->name('destroy')->middleware('permission:fuel.delete');
});
