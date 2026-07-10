<?php

use App\Http\Controllers\Complaint\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::prefix('complaints')->name('complaint.')->group(function () {
    Route::get('/', [ComplaintController::class, 'index'])->name('index')->middleware('permission:complaint.view');
    Route::get('/create', [ComplaintController::class, 'create'])->name('create')->middleware('permission:complaint.create');
    Route::post('/', [ComplaintController::class, 'store'])->name('store')->middleware('permission:complaint.create');
    Route::get('/{complaint}', [ComplaintController::class, 'show'])->name('show')->middleware('permission:complaint.view');
    Route::post('/{complaint}/processing', [ComplaintController::class, 'markProcessing'])->name('processing')->middleware('permission:complaint.edit');
    Route::post('/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('resolve')->middleware('permission:complaint.edit');
});
