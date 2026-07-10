<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkStockMovement extends GaModel
{
    protected $casts = ['quantity' => 'integer'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(AtkItem::class, 'atk_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
