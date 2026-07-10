<?php

use App\Http\Controllers\Toilet\ToiletInspectionController;
use Illuminate\Support\Facades\Route;

Route::prefix('toilet')->name('toilet.')->group(function () {
    Route::get('/', [ToiletInspectionController::class, 'index'])->name('index')->middleware('permission:toilet.view');
    Route::get('/create', [ToiletInspectionController::class, 'create'])->name('create')->middleware('permission:toilet.create');
    Route::post('/', [ToiletInspectionController::class, 'store'])->name('store')->middleware('permission:toilet.create');
    Route::get('/export/pdf', [ToiletInspectionController::class, 'exportPdf'])->name('export.pdf')->middleware('permission:toilet.export');
    Route::get('/export/excel', [ToiletInspectionController::class, 'exportExcel'])->name('export.excel')->middleware('permission:toilet.export');
    Route::get('/{toiletInspection}', [ToiletInspectionController::class, 'show'])->name('show')->middleware('permission:toilet.view');
    Route::get('/{toiletInspection}/edit', [ToiletInspectionController::class, 'edit'])->name('edit')->middleware('permission:toilet.edit');
    Route::put('/{toiletInspection}', [ToiletInspectionController::class, 'update'])->name('update')->middleware('permission:toilet.edit');
    Route::delete('/{toiletInspection}', [ToiletInspectionController::class, 'destroy'])->name('destroy')->middleware('permission:toilet.delete');
});
