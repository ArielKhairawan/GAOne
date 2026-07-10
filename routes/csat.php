<?php

use App\Http\Controllers\CsatController;
use Illuminate\Support\Facades\Route;

Route::prefix('csat')->name('csat.')->middleware('permission:csat.view')->group(function () {
    Route::get('/', [CsatController::class, 'index'])->name('index');
    Route::get('/{survey}', [CsatController::class, 'show'])->name('show');
    Route::post('/{survey}', [CsatController::class, 'store'])->name('store')->middleware('permission:csat.create');
});
