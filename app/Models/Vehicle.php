<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends GaModel
{
    use SoftDeletes;

    protected $casts = [
        'tahun' => 'integer',
    ];

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    public function driverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Nama driver untuk ditampilkan: utamakan akun user (driver_id) bila ada,
     * jatuh ke teks bebas lama (driver) untuk data lama yang belum migrasi.
     */
    public function getDriverNameAttribute(): ?string
    {
        return $this->driverUser?->name ?? $this->driver;
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('driver_id', $userId);
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
            $q->where('plat_nomor', 'like', "%{$term}%")
                ->orWhere('merk', 'like', "%{$term}%")
                ->orWhere('driver', 'like', "%{$term}%");
        });
    }
}
