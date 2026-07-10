<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReorderAlert extends GaModel
{
    protected $casts = ['is_resolved' => 'boolean'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(AtkItem::class, 'atk_item_id');
    }
}
