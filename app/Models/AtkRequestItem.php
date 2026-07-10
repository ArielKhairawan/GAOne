<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkRequestItem extends GaModel
{
    protected $casts = ['quantity' => 'integer'];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AtkRequest::class, 'atk_request_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(AtkItem::class, 'atk_item_id');
    }
}
