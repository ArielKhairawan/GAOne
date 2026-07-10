<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\Vehicle;

class VehicleNotificationService
{
    /**
     * Notifikasi ketika status kendaraan berubah menjadi Servis atau Tidak Aktif,
     * sehingga tim terkait dapat menyiapkan kendaraan pengganti bila diperlukan.
     */
    public function handleStatusChange(Vehicle $vehicle, ?string $previousStatus): void
    {
        if ($previousStatus === $vehicle->status) {
            return;
        }

        if (! in_array($vehicle->status, ['servis', 'tidak_aktif'], true)) {
            return;
        }

        $label = config('monitoring.vehicle_statuses')[$vehicle->status] ?? $vehicle->status;

        SystemNotification::create([
            'user_id' => null,
            'type' => 'vehicle.status_changed',
            'channel' => 'in-app',
            'title' => 'Status Kendaraan Berubah',
            'body' => "Kendaraan {$vehicle->plat_nomor} ({$vehicle->jenis_kendaraan}) kini berstatus \"{$label}\".",
            'sent_at' => now(),
        ]);
    }
}
