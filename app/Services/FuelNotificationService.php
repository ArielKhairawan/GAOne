<?php

namespace App\Services;

use App\Models\FuelLog;
use App\Models\SystemNotification;
use Illuminate\Support\Carbon;

class FuelNotificationService
{
    public function handleAfterSave(FuelLog $fuelLog): void
    {
        $this->checkConsumption($fuelLog);
        $this->checkMonthlyBudget($fuelLog->tanggal_pengisian);
    }

    /**
     * Notifikasi ketika konsumsi BBM kendaraan tertentu terlalu boros.
     */
    public function checkConsumption(FuelLog $fuelLog): void
    {
        $threshold = (float) config('monitoring.fuel_consumption_min_kmpl');

        if ($fuelLog->konsumsi_bbm === null || (float) $fuelLog->konsumsi_bbm >= $threshold) {
            return;
        }

        $plat = $fuelLog->vehicle?->plat_nomor ?? 'Tidak diketahui';

        SystemNotification::create([
            'user_id' => null,
            'type' => 'fuel.consumption_warning',
            'channel' => 'in-app',
            'title' => 'Konsumsi BBM Boros',
            'body' => "Kendaraan {$plat} mencatat konsumsi BBM {$fuelLog->konsumsi_bbm} km/liter, di bawah ambang batas {$threshold} km/liter.",
            'sent_at' => now(),
        ]);
    }

    /**
     * Notifikasi ketika total pengeluaran BBM bulan ini melebihi batas anggaran.
     * Hanya dikirim sekali per bulan untuk menghindari spam.
     */
    public function checkMonthlyBudget(Carbon|string $referenceDate): void
    {
        $date = $referenceDate instanceof Carbon ? $referenceDate : Carbon::parse($referenceDate);
        $threshold = (float) config('monitoring.fuel_budget_threshold_monthly');

        $totalBulanIni = FuelLog::query()
            ->whereBetween('tanggal_pengisian', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ])
            ->sum('total_harga');

        if ((float) $totalBulanIni < $threshold) {
            return;
        }

        $periode = $date->translatedFormat('F Y');

        $alreadyNotified = SystemNotification::query()
            ->where('type', 'fuel.budget_exceeded')
            ->whereDate('created_at', '>=', $date->copy()->startOfMonth())
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        SystemNotification::create([
            'user_id' => null,
            'type' => 'fuel.budget_exceeded',
            'channel' => 'in-app',
            'title' => 'Pengeluaran BBM Melebihi Batas',
            'body' => "Total pengeluaran BBM periode {$periode} telah mencapai Rp ".number_format($totalBulanIni, 0, ',', '.')." dan melebihi batas anggaran Rp ".number_format($threshold, 0, ',', '.').'.',
            'sent_at' => now(),
        ]);
    }
}
