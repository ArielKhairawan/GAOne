<?php

namespace App\Services\Inventory;

use App\Models\AtkItem;
use App\Models\AtkStockMovement;
use App\Repositories\Contracts\AtkItemRepositoryInterface;
use App\Services\AtkNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AtkInventoryService
{
    public function __construct(
        private AtkItemRepositoryInterface $items,
        private AtkNotificationService $notifier,
    ) {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->items->paginate($filters, $perPage);
    }

    public function find(int $id): AtkItem
    {
        return $this->items->find($id);
    }

    public function create(array $data): AtkItem
    {
        return $this->items->create($data);
    }

    public function update(AtkItem $item, array $data): AtkItem
    {
        return $this->items->update($item, $data);
    }

    public function delete(AtkItem $item): bool
    {
        return $this->items->delete($item);
    }

    public function options(): Collection
    {
        return $this->items->all();
    }

    public function lowStockItems(): Collection
    {
        return $this->items->lowStock();
    }

    /**
     * Catat Barang Masuk: tambah stok + buat record movement, lalu cek
     * notifikasi (jaga-jaga jika ternyata sebelumnya berstatus habis/menipis
     * dan sekarang sudah aman, tidak perlu notifikasi tambahan).
     */
    public function stockIn(AtkItem $item, int $quantity, ?string $notes, int $userId): AtkStockMovement
    {
        return DB::transaction(function () use ($item, $quantity, $notes, $userId) {
            $updated = $this->items->adjustStock($item, $quantity);

            return AtkStockMovement::create([
                'atk_item_id' => $updated->id,
                'user_id' => $userId,
                'type' => 'masuk',
                'quantity' => $quantity,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Catat Barang Keluar: kurangi stok + buat record movement, lalu cek
     * apakah perlu notifikasi stok menipis/habis.
     */
    public function stockOut(AtkItem $item, int $quantity, ?string $notes, int $userId, ?string $referenceType = null, ?int $referenceId = null): AtkStockMovement
    {
        return DB::transaction(function () use ($item, $quantity, $notes, $userId, $referenceType, $referenceId) {
            $updated = $this->items->adjustStock($item, -$quantity);

            $movement = AtkStockMovement::create([
                'atk_item_id' => $updated->id,
                'user_id' => $userId,
                'type' => 'keluar',
                'quantity' => $quantity,
                'notes' => $notes,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            $this->notifier->checkStockLevel($updated->refresh());

            return $movement;
        });
    }
}
