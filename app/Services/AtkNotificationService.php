<?php

namespace App\Services;

use App\Models\AtkItem;
use App\Models\AtkRequest;
use App\Models\SystemNotification;

class AtkNotificationService
{
    public function checkStockLevel(AtkItem $item): void
    {
        if ($item->stock <= 0) {
            $this->notify('atk.stock_out', 'Stok Habis', "Stok \"{$item->name}\" telah habis (0 {$item->satuan}).");

            return;
        }

        if ($item->stock <= $item->minimum_stock) {
            $this->notify('atk.stock_low', 'Stok Menipis', "Stok \"{$item->name}\" tersisa {$item->stock} {$item->satuan}, di bawah ambang minimum {$item->minimum_stock} {$item->satuan}.");
        }
    }

    public function requestSubmitted(AtkRequest $request): void
    {
        $this->notify('atk.request_submitted', 'Permintaan ATK Baru', "Permintaan ATK baru dari {$request->requester?->name} (departemen {$request->department}) menunggu persetujuan.");
    }

    public function requestApproved(AtkRequest $request): void
    {
        SystemNotification::create([
            'user_id' => $request->user_id,
            'type' => 'atk.request_approved',
            'channel' => 'in-app',
            'title' => 'Permintaan ATK Disetujui',
            'body' => 'Permintaan ATK Anda telah disetujui dan sedang diproses.',
            'sent_at' => now(),
        ]);
    }

    public function requestRejected(AtkRequest $request, ?string $reason = null): void
    {
        SystemNotification::create([
            'user_id' => $request->user_id,
            'type' => 'atk.request_rejected',
            'channel' => 'in-app',
            'title' => 'Permintaan ATK Ditolak',
            'body' => $reason ? "Permintaan ATK Anda ditolak. Alasan: {$reason}" : 'Permintaan ATK Anda ditolak.',
            'sent_at' => now(),
        ]);
    }

    private function notify(string $type, string $title, string $body): void
    {
        SystemNotification::create([
            'user_id' => null,
            'type' => $type,
            'channel' => 'in-app',
            'title' => $title,
            'body' => $body,
            'sent_at' => now(),
        ]);
    }
}
