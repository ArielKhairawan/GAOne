<?php

namespace App\Services\Meeting;

use App\Models\Complaint;
use App\Services\CsatService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ComplaintService
{
    public function __construct(private CsatService $csat)
    {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return Complaint::query()
            ->with(['user', 'resolver'])
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Complaint
    {
        return Complaint::with(['user', 'resolver'])->findOrFail($id);
    }

    public function create(array $data, int $userId): Complaint
    {
        return Complaint::create([
            ...$data,
            'user_id' => $userId,
            'status' => 'menunggu',
        ]);
    }

    public function markProcessing(Complaint $complaint): void
    {
        $complaint->update(['status' => 'diproses']);
    }

    public function resolve(Complaint $complaint, int $resolverId): void
    {
        $complaint->update([
            'status' => 'selesai',
            'resolved_by' => $resolverId,
            'resolved_at' => now(),
        ]);

        $this->csat->requestFeedback($complaint, 'complaint', $complaint->user_id);
    }
}
