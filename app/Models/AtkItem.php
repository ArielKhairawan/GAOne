<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AtkItem extends GaModel
{
    protected $appends = ['status'];

    protected $casts = [
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AtkCategory::class, 'atk_category_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(AtkStockMovement::class);
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(AtkRequestItem::class);
    }

    /**
     * Status persediaan dihitung langsung dari stock vs minimum_stock,
     * bukan kolom tersimpan, supaya tidak ada risiko data tidak sinkron.
     */
    public function getStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }

        if ($this->stock <= $this->minimum_stock) {
            return 'stok_menipis';
        }

        return 'tersedia';
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")->orWhere('code', 'like', "%{$term}%");
        });
    }

    public function scopeCategory($query, ?int $categoryId)
    {
        return $categoryId ? $query->where('atk_category_id', $categoryId) : $query;
    }
}
