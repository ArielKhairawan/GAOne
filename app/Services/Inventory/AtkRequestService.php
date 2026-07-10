<?php

namespace App\Services\Inventory;

use App\Models\AtkItem;
use App\Models\AtkRequest;
use App\Services\ApprovalEngine;
use App\Services\AtkNotificationService;
use App\Services\CsatService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AtkRequestService
{
    public function __construct(
        private ApprovalEngine $approvalEngine,
        private AtkInventoryService $inventory,
        private AtkNotificationService $notifier,
        private CsatService $csat,
    ) {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return AtkRequest::query()
            ->with(['requester', 'items.item'])
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): AtkRequest
    {
        return AtkRequest::with(['requester', 'items.item', 'approvalInstances.approvalWorkflow'])->findOrFail($id);
    }

    /**
     * Buat permintaan lalu langsung submit ke workflow approval
     * (Karyawan -> GA Staff -> Manager).
     *
     * @param  array<int, array{atk_item_id:int, quantity:int}>  $lines
     */
    public function submit(array $data, array $lines, int $userId): AtkRequest
    {
        return DB::transaction(function () use ($data, $lines, $userId) {
            $request = AtkRequest::create([
                'user_id' => $userId,
                'department' => $data['department'],
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($lines as $line) {
                $request->items()->create([
                    'atk_item_id' => $line['atk_item_id'],
                    'quantity' => $line['quantity'],
                ]);
            }

            $this->approvalEngine->submit($request, 'atk');
            $this->notifier->requestSubmitted($request->fresh(['requester']));

            return $request->fresh(['items.item']);
        });
    }

    public function act(AtkRequest $request, string $action, ?string $notes = null): void
    {
        $instance = $request->approvalInstances()->where('status', 'pending')->latest()->firstOrFail();

        if ($action === 'approve') {
            $this->assertStockAvailable($request);
        }

        $this->approvalEngine->act($instance, $action, $notes);
        $request->refresh();

        if ($request->status === 'approved') {
            $this->fulfillStock($request);
            $this->notifier->requestApproved($request);
            $this->csat->requestFeedback($request, 'atk', $request->user_id);
        } elseif ($request->status === 'rejected') {
            $this->notifier->requestRejected($request, $notes);
        }
    }

    private function assertStockAvailable(AtkRequest $request): void
    {
        foreach ($request->items as $line) {
            /** @var AtkItem $item */
            $item = $line->item;
            if ($item && $item->stock < $line->quantity) {
                throw ValidationException::withMessages([
                    'stock' => "Stok \"{$item->name}\" tidak cukup (tersedia {$item->stock}, diminta {$line->quantity}).",
                ]);
            }
        }
    }

    private function fulfillStock(AtkRequest $request): void
    {
        foreach ($request->items as $line) {
            if ($line->item) {
                $this->inventory->stockOut(
                    $line->item,
                    $line->quantity,
                    "Pemenuhan permintaan ATK #{$request->id}",
                    $request->user_id,
                    AtkRequest::class,
                    $request->id,
                );
            }
        }
    }
}
