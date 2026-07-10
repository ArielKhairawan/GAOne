<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Setiap modul memiliki file route terpisah agar mudah dipelihara.
    require __DIR__.'/users.php';
    require __DIR__.'/approvals.php';
    require __DIR__.'/reports.php';
    require __DIR__.'/modules.php';

    // Modul Monitoring Operasional
    require __DIR__.'/fuel.php';
    require __DIR__.'/vehicle.php';
    require __DIR__.'/toilet.php';

    // Modul baru: Inventaris ATK, Meeting & Konsumsi, Pengaduan, CSAT
    require __DIR__.'/atk.php';
    require __DIR__.'/meeting.php';
    require __DIR__.'/consumption.php';
    require __DIR__.'/complaint.php';
    require __DIR__.'/csat.php';

    // Manajemen Role & Permission
    require __DIR__.'/admin.php';
});
