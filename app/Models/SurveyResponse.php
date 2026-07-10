<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends GaModel
{
    protected $casts = [
        'speed_score' => 'integer',
        'service_score' => 'integer',
        'satisfaction_score' => 'integer',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}
