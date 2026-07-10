<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Survey extends GaModel
{
    protected $casts = ['sent_at' => 'datetime', 'completed_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function surveyable(): MorphTo
    {
        return $this->morphTo();
    }

    public function response(): HasOne
    {
        return $this->hasOne(SurveyResponse::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
