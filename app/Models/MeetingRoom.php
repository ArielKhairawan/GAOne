<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingRoom extends GaModel
{
    use SoftDeletes;

    protected $casts = [
        'fasilitas' => 'array',
        'kapasitas' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(MeetingBooking::class);
    }

    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('nama_ruangan', 'like', "%{$term}%")
                ->orWhere('kode_ruangan', 'like', "%{$term}%")
                ->orWhere('lokasi', 'like', "%{$term}%");
        });
    }

    /**
     * Cek apakah ruangan tersedia pada rentang waktu tertentu (tidak bentrok
     * dengan booking lain yang masih aktif). $excludeBookingId dipakai saat
     * mengedit booking yang sudah ada agar tidak membandingkan dengan dirinya
     * sendiri.
     */
    public function isAvailableAt(string $tanggal, string $jamMulai, string $jamSelesai, ?int $excludeBookingId = null): bool
    {
        if (in_array($this->status, ['maintenance', 'tidak_aktif', 'digunakan'], true)) {
            return false;
        }

        $conflict = $this->bookings()
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['draft', 'submitted', 'approved'])
            ->when($excludeBookingId, fn ($q, $id) => $q->where('id', '!=', $id))
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })
            ->exists();

        return ! $conflict;
    }
}
