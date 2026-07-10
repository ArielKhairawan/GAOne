<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsumptionRequest extends GaModel
{
    use SoftDeletes;

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_peserta' => 'integer',
        'jenis_konsumsi' => 'array',
    ];

    public function meetingBooking(): BelongsTo
    {
        return $this->belongsTo(MeetingBooking::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvalInstances(): MorphMany
    {
        return $this->morphMany(ApprovalInstance::class, 'approvable');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('tanggal', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('tanggal', '<=', $v));
    }
}
