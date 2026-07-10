<?php

namespace App\Services\Meeting;

use App\Models\ConsumptionRequest;
use App\Services\ApprovalEngine;
use App\Services\CsatService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ConsumptionRequestService
{
    public function __construct(
        private ApprovalEngine $approvalEngine,
        private CsatService $csat,
    ) {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return ConsumptionRequest::query()
            ->with(['requester', 'meetingBooking'])
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): ConsumptionRequest
    {
        return ConsumptionRequest::with(['requester', 'meetingBooking', 'approvalInstances'])->findOrFail($id);
    }

    public function submit(array $data, int $userId): ConsumptionRequest
    {
        return DB::transaction(function () use ($data, $userId) {
            $request = ConsumptionRequest::create([
                ...$data,
                'user_id' => $userId,
                'status' => 'draft',
            ]);

            $this->approvalEngine->submit($request, 'consumption');

            return $request->fresh();
        });
    }

    public function act(ConsumptionRequest $request, string $action, ?string $notes = null): void
    {
        $instance = $request->approvalInstances()->where('status', 'pending')->latest()->firstOrFail();
        $this->approvalEngine->act($instance, $action, $notes);
        $request->refresh();
    }

    public function markProcessing(ConsumptionRequest $request): void
    {
        $request->update(['status' => 'diproses']);
    }

    public function markCompleted(ConsumptionRequest $request): void
    {
        $request->update(['status' => 'selesai']);
        $this->csat->requestFeedback($request, 'consumption', $request->user_id);
    }
}
