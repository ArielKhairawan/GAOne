<?php

namespace App\Services;

use App\Models\MeetingBooking;
use App\Models\SystemNotification;

class MeetingNotificationService
{
    public function bookingSubmitted(MeetingBooking $booking): void
    {
        $this->broadcast(
            'meeting.booking_submitted',
            'Booking Ruang Meeting Baru',
            "Booking baru dari {$booking->requester?->name} untuk ruang {$booking->room?->nama_ruangan} pada {$booking->tanggal->format('d M Y')} menunggu persetujuan."
        );
    }

    public function bookingApproved(MeetingBooking $booking): void
    {
        $this->toUser($booking->user_id, 'meeting.booking_approved', 'Booking Disetujui', "Booking ruang {$booking->room?->nama_ruangan} pada {$booking->tanggal->format('d M Y')} telah disetujui.");
    }

    public function bookingRejected(MeetingBooking $booking, ?string $reason = null): void
    {
        $body = "Booking ruang {$booking->room?->nama_ruangan} pada {$booking->tanggal->format('d M Y')} ditolak.";
        if ($reason) {
            $body .= " Alasan: {$reason}";
        }
        $this->toUser($booking->user_id, 'meeting.booking_rejected', 'Booking Ditolak', $body);
    }

    private function toUser(int $userId, string $type, string $title, string $body): void
    {
        SystemNotification::create([
            'user_id' => $userId, 'type' => $type, 'channel' => 'in-app',
            'title' => $title, 'body' => $body, 'sent_at' => now(),
        ]);
    }

    private function broadcast(string $type, string $title, string $body): void
    {
        SystemNotification::create([
            'user_id' => null, 'type' => $type, 'channel' => 'in-app',
            'title' => $title, 'body' => $body, 'sent_at' => now(),
        ]);
    }
}
