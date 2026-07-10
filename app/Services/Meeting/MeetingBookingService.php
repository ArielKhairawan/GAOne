<?php

namespace App\Services\Meeting;

use App\Models\MeetingBooking;
use App\Models\MeetingRoom;
use App\Services\ApprovalEngine;
use App\Services\CsatService;
use App\Services\MeetingNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MeetingBookingService
{
    public function __construct(
        private ApprovalEngine $approvalEngine,
        private MeetingNotificationService $notifier,
        private CsatService $csat,
    ) {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return MeetingBooking::query()
            ->with(['room', 'requester'])
            ->filter($filters)
            ->orderByDesc('tanggal')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): MeetingBooking
    {
        return MeetingBooking::with(['room', 'requester', 'consumptionRequest'])->findOrFail($id);
    }

    /**
     * @param  array<string,mixed>  $consumptionData  Diisi hanya jika butuh_konsumsi = true.
     */
    public function submit(array $data, ?array $consumptionData, int $userId): MeetingBooking
    {
        return DB::transaction(function () use ($data, $consumptionData, $userId) {
            $room = MeetingRoom::findOrFail($data['meeting_room_id']);

            if ((int) $data['jumlah_peserta'] > $room->kapasitas) {
                throw ValidationException::withMessages([
                    'jumlah_peserta' => "Jumlah peserta ({$data['jumlah_peserta']}) melebihi kapasitas ruangan ({$room->kapasitas}).",
                ]);
            }

            if (! $room->isAvailableAt($data['tanggal'], $data['jam_mulai'], $data['jam_selesai'])) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Ruangan tidak tersedia / bentrok dengan booking lain pada rentang waktu tersebut.',
                ]);
            }

            $booking = MeetingBooking::create([
                ...$data,
                'user_id' => $userId,
                'butuh_konsumsi' => (bool) ($data['butuh_konsumsi'] ?? false),
                'status' => 'draft',
            ]);

            if ($booking->butuh_konsumsi && $consumptionData) {
                $booking->consumptionRequest()->create([
                    ...$consumptionData,
                    'meeting_booking_id' => $booking->id,
                    'user_id' => $userId,
                    'tanggal' => $data['tanggal'],
                    'status' => 'draft',
                ]);
            }

            $this->approvalEngine->submit($booking, 'meeting');
            $this->notifier->bookingSubmitted($booking->fresh(['requester', 'room']));

            return $booking->fresh();
        });
    }

    public function act(MeetingBooking $booking, string $action, ?string $notes = null): void
    {
        $instance = $booking->approvalInstances()->where('status', 'pending')->latest()->firstOrFail();
        $this->approvalEngine->act($instance, $action, $notes);
        $booking->refresh();

        if ($booking->status === 'approved') {
            $booking->consumptionRequest()->update(['status' => 'approved']);
            $this->notifier->bookingApproved($booking);
        } elseif ($booking->status === 'rejected') {
            $booking->consumptionRequest()->update(['status' => 'rejected']);
            $this->notifier->bookingRejected($booking, $notes);
        }
    }

    /**
     * Tandai booking yang sudah disetujui sebagai selesai (acara sudah
     * berlangsung), lalu picu permintaan feedback CSAT ke pemohon.
     */
    public function markCompleted(MeetingBooking $booking): void
    {
        if ($booking->status !== 'approved') {
            throw ValidationException::withMessages(['status' => 'Hanya booking yang sudah disetujui dapat ditandai selesai.']);
        }

        $booking->update(['status' => 'selesai']);
        $booking->consumptionRequest()->update(['status' => 'selesai']);
        $this->csat->requestFeedback($booking, 'meeting', $booking->user_id);
    }

    public function checkAvailability(int $roomId, string $tanggal, string $jamMulai, string $jamSelesai, ?int $excludeBookingId = null): bool
    {
        return MeetingRoom::findOrFail($roomId)->isAvailableAt($tanggal, $jamMulai, $jamSelesai, $excludeBookingId);
    }
}
