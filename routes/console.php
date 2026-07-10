<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek lokasi WC yang belum diinspeksi sesuai jadwal (lihat config/monitoring.php
// untuk mengatur interval jam) dan kirim notifikasi otomatis bila terlambat.
Schedule::command('monitoring:check-alerts')->everyFourHours();
