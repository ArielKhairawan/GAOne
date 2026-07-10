<?php

use App\Http\Controllers\Consumption\ConsumptionRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('consumption')->name('consumption.')->group(function () {
    Route::get('/', [ConsumptionRequestController::class, 'index'])->name('index')->middleware('permission:consumption.view');
    Route::get('/create', [ConsumptionRequestController::class, 'create'])->name('create')->middleware('permission:consumption.create');
    Route::post('/', [ConsumptionRequestController::class, 'store'])->name('store')->middleware('permission:consumption.create');
    Route::get('/{consumption_request}', [ConsumptionRequestController::class, 'show'])->name('show')->middleware('permission:consumption.view');
    Route::post('/{consumption_request}/act', [ConsumptionRequestController::class, 'act'])->name('act')->middleware('permission:consumption.approve');
    Route::post('/{consumption_request}/processing', [ConsumptionRequestController::class, 'markProcessing'])->name('processing')->middleware('permission:consumption.edit');
    Route::post('/{consumption_request}/complete', [ConsumptionRequestController::class, 'complete'])->name('complete')->middleware('permission:consumption.edit');
});
