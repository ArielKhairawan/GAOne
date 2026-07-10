<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToiletInspection extends GaModel
{
    use SoftDeletes;

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ToiletInspectionItem::class, 'toilet_inspection_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function petugasUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Utamakan nama dari akun user (petugas_id) — wajib untuk data baru.
     * Jatuh ke teks bebas lama (petugas) hanya untuk data sebelum migrasi ini.
     */
    public function getPetugasNameAttribute(): ?string
    {
        return $this->petugasUser?->name ?? $this->petugas;
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('petugas_id', $userId);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['lokasi'] ?? null, fn ($q, $v) => $q->where('lokasi', $v))
            ->when($filters['petugas'] ?? null, function ($q, $v) {
                $q->where(function ($sub) use ($v) {
                    $sub->where('petugas', 'like', "%{$v}%")
                        ->orWhereHas('petugasUser', fn ($pq) => $pq->where('name', 'like', "%{$v}%"));
                });
            })
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('tanggal', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('tanggal', '<=', $v));
    }
}
