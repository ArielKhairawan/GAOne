<?php

namespace App\Console\Commands;

use App\Services\Monitoring\ToiletInspectionService;
use Illuminate\Console\Command;

class CheckMonitoringAlerts extends Command
{
    protected $signature = 'monitoring:check-alerts';

    protected $description = 'Periksa lokasi WC yang belum diinspeksi sesuai jadwal dan kirim notifikasi bila terlambat.';

    public function handle(ToiletInspectionService $toiletInspections): int
    {
        $toiletInspections->checkOverdueLocations();

        $this->info('Pengecekan jadwal inspeksi WC selesai.');

        return self::SUCCESS;
    }
}
