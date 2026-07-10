<?php

use App\Http\Controllers\Atk\AtkItemController;
use App\Http\Controllers\Atk\AtkRequestController;
use App\Http\Controllers\Atk\AtkStockMovementController;
use Illuminate\Support\Facades\Route;

Route::prefix('atk/items')->name('atk.items.')->group(function () {
    Route::get('/', [AtkItemController::class, 'index'])->name('index')->middleware('permission:atk.view');
    Route::get('/create', [AtkItemController::class, 'create'])->name('create')->middleware('permission:atk.create');
    Route::post('/', [AtkItemController::class, 'store'])->name('store')->middleware('permission:atk.create');
    Route::get('/export/pdf', [AtkItemController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:atk.export');
    Route::get('/export/excel', [AtkItemController::class, 'exportExcel'])->name('export.excel')->middleware('permission:atk.export');
    Route::get('/{atk_item}/edit', [AtkItemController::class, 'edit'])->name('edit')->middleware('permission:atk.edit');
    Route::put('/{atk_item}', [AtkItemController::class, 'update'])->name('update')->middleware('permission:atk.edit');
    Route::delete('/{atk_item}', [AtkItemController::class, 'destroy'])->name('destroy')->middleware('permission:atk.delete');
});

Route::prefix('atk/stock-in')->name('atk.stock-in.')->middleware('permission:atk.edit')->group(function () {
    Route::get('/', [AtkStockMovementController::class, 'indexIn'])->name('index');
    Route::get('/create', [AtkStockMovementController::class, 'createIn'])->name('create');
    Route::post('/', [AtkStockMovementController::class, 'storeIn'])->name('store');
});

Route::prefix('atk/stock-out')->name('atk.stock-out.')->middleware('permission:atk.edit')->group(function () {
    Route::get('/', [AtkStockMovementController::class, 'indexOut'])->name('index');
    Route::get('/create', [AtkStockMovementController::class, 'createOut'])->name('create');
    Route::post('/', [AtkStockMovementController::class, 'storeOut'])->name('store');
});

Route::prefix('atk/requests')->name('atk.requests.')->group(function () {
    Route::get('/', [AtkRequestController::class, 'index'])->name('index')->middleware('permission:atk.view');
    Route::get('/create', [AtkRequestController::class, 'create'])->name('create')->middleware('permission:atk.create');
    Route::post('/', [AtkRequestController::class, 'store'])->name('store')->middleware('permission:atk.create');
    Route::get('/{atk_request}', [AtkRequestController::class, 'show'])->name('show')->middleware('permission:atk.view');
    Route::post('/{atk_request}/act', [AtkRequestController::class, 'act'])->name('act')->middleware('permission:atk.approve');
});
