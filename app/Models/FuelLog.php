<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelLog extends GaModel
{
    use SoftDeletes;

    protected $casts = [
        'tanggal_pengisian' => 'date',
        'harga_per_liter' => 'decimal:2',
        'jumlah_liter' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'konsumsi_bbm' => 'decimal:2',
        'kilometer_awal' => 'integer',
        'kilometer_akhir' => 'integer',
        'jarak_tempuh' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function driverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function getDriverNameAttribute(): ?string
    {
        return $this->driverUser?->name ?? $this->driver;
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('driver_id', $userId)
                ->orWhereHas('vehicle', fn ($vq) => $vq->where('driver_id', $userId));
        });
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_pengisian', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_pengisian', '<=', $v))
            ->when($filters['driver'] ?? null, fn ($q, $v) => $q->where('driver', 'like', "%{$v}%"))
            ->when($filters['jenis_bahan_bakar'] ?? null, fn ($q, $v) => $q->where('jenis_bahan_bakar', $v))
            ->when($filters['vehicle_id'] ?? null, fn ($q, $v) => $q->where('vehicle_id', $v))
            ->when($filters['owner_id'] ?? null, fn ($q, $v) => $q->ownedBy($v))
            ->when($filters['plat_nomor'] ?? null, function ($q, $v) {
                $q->whereHas('vehicle', fn ($vq) => $vq->where('plat_nomor', 'like', "%{$v}%"));
            });
    }
}
