<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\ToiletInspection;
use Illuminate\Support\Carbon;

class ToiletNotificationService
{
    public function handleAfterSave(ToiletInspection $inspection): void
    {
        $this->checkDirtyStatus($inspection);
    }

    /**
     * Notifikasi ketika ada inspeksi dengan status Kotor.
     */
    public function checkDirtyStatus(ToiletInspection $inspection): void
    {
        if ($inspection->status !== 'kotor') {
            return;
        }

        SystemNotification::create([
            'user_id' => null,
            'type' => 'toilet.dirty_status',
            'channel' => 'in-app',
            'title' => 'Inspeksi WC: Status Kotor',
            'body' => "Lokasi {$inspection->lokasi} dilaporkan dalam status Kotor pada {$inspection->tanggal->format('d M Y')} pukul {$inspection->jam} oleh {$inspection->petugas}.",
            'sent_at' => now(),
        ]);
    }

    /**
     * Notifikasi ketika sebuah lokasi belum diperiksa sesuai jadwal
     * (dipanggil secara terjadwal, lihat App\Console\Commands\CheckMonitoringAlerts).
     */
    public function checkOverdueInspections(\Illuminate\Support\Collection $latestPerLocation): void
    {
        $intervalHours = (int) config('monitoring.toilet_inspection_interval_hours');

        foreach ($latestPerLocation as $row) {
            $lokasi = $row['lokasi'];
            $latest = $row['inspection'];

            $lastCheckedAt = $latest
                ? Carbon::parse($latest->tanggal->toDateString().' '.$latest->jam)
                : null;

            $isOverdue = ! $lastCheckedAt || $lastCheckedAt->diffInHours(now()) >= $intervalHours;

            if (! $isOverdue) {
                continue;
            }

            $alreadyNotified = SystemNotification::query()
                ->where('type', 'toilet.overdue')
                ->where('title', 'like', "%{$lokasi}%")
                ->where('created_at', '>=', now()->subHours($intervalHours))
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            SystemNotification::create([
                'user_id' => null,
                'type' => 'toilet.overdue',
                'channel' => 'in-app',
                'title' => "Inspeksi Terlambat: {$lokasi}",
                'body' => $lastCheckedAt
                    ? "Lokasi {$lokasi} belum diperiksa sejak {$lastCheckedAt->format('d M Y H:i')} (lebih dari {$intervalHours} jam)."
                    : "Lokasi {$lokasi} belum pernah memiliki data inspeksi.",
                'sent_at' => now(),
            ]);
        }
    }
}
