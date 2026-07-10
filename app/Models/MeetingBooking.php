<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingBooking extends GaModel
{
    use SoftDeletes;

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_peserta' => 'integer',
        'butuh_konsumsi' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class, 'meeting_room_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function consumptionRequest(): HasMany
    {
        return $this->hasMany(ConsumptionRequest::class);
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
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('tanggal', '<=', $v))
            ->when($filters['meeting_room_id'] ?? null, fn ($q, $v) => $q->where('meeting_room_id', $v));
    }
}
