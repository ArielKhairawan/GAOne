<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('users', UserController::class)->except('show')->middleware('permission:user.view|user.create|user.edit|user.delete');
Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate')->middleware('permission:user.edit');
Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate')->middleware('permission:user.edit');
Route::get('/users-export', [UserController::class, 'export'])->name('users.export')->middleware('permission:user.export');
