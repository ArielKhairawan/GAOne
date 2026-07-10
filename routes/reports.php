<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:report.view');
Route::get('/reports/{type}/export', [ReportController::class, 'export'])->name('reports.export')->middleware('permission:report.export');
