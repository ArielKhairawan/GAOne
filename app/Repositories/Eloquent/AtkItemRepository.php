<?php

namespace App\Repositories\Eloquent;

use App\Models\AtkItem;
use App\Repositories\Contracts\AtkItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AtkItemRepository implements AtkItemRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return AtkItem::query()
            ->with('category')
            ->search($filters['search'] ?? null)
            ->category($filters['atk_category_id'] ?? null)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): AtkItem
    {
        return AtkItem::with('category')->findOrFail($id);
    }

    public function create(array $data): AtkItem
    {
        return AtkItem::create($data);
    }

    public function update(AtkItem $item, array $data): AtkItem
    {
        $item->update($data);

        return $item;
    }

    public function delete(AtkItem $item): bool
    {
        return $item->delete();
    }

    public function all(): Collection
    {
        return AtkItem::query()->orderBy('name')->get();
    }

    public function lowStock(): Collection
    {
        return AtkItem::query()->whereColumn('stock', '<=', 'minimum_stock')->orderBy('stock')->get();
    }

    /**
     * Tambah/kurangi stok secara atomik (row lock) supaya aman dari race
     * condition saat dua transaksi stok berjalan bersamaan.
     */
    public function adjustStock(AtkItem $item, int $delta): AtkItem
    {
        return DB::transaction(function () use ($item, $delta) {
            $locked = AtkItem::query()->lockForUpdate()->findOrFail($item->id);
            $locked->stock = max(0, $locked->stock + $delta);
            $locked->save();

            return $locked;
        });
    }
}
