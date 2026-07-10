<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AtkCategory extends GaModel
{
    public function items(): HasMany
    {
        return $this->hasMany(AtkItem::class);
    }
}
