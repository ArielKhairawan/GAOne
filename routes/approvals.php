<?php

use App\Http\Controllers\ApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index')->middleware('permission:approval.view');
Route::post('/approvals/{approval}/act', [ApprovalController::class, 'act'])->name('approvals.act')->middleware('permission:approval.approve');
