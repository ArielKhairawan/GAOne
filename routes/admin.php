<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/roles')->name('admin.roles.')->middleware('permission:user.view')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:user.create');
    Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:user.create');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:user.edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update')->middleware('permission:user.edit');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:user.delete');
});

Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index')->middleware('permission:user.view');
