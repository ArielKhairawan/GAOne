<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToiletInspectionItem extends GaModel
{
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(ToiletInspection::class, 'toilet_inspection_id');
    }
}
