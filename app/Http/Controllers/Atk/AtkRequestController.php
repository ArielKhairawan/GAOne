<?php

namespace App\Http\Controllers\Atk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Atk\AtkRequestActionRequest;
use App\Http\Requests\Atk\StoreAtkRequestRequest;
use App\Models\AtkRequest;
use App\Services\Inventory\AtkInventoryService;
use App\Services\Inventory\AtkRequestService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AtkRequestController extends Controller
{
    public function __construct(
        private AtkRequestService $requests,
        private AtkInventoryService $inventory,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'department']);

        // Karyawan hanya melihat permintaan miliknya sendiri.
        if ($request->user()->hasRole('Karyawan') && ! $request->user()->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            $filters['user_id'] = $request->user()->id;
        }

        return view('atk.requests.index', [
            'requests' => AtkRequest::query()
                ->with(['requester', 'items.item'])
                ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
                ->filter($filters)
                ->latest()
                ->paginate((int) config('monitoring.per_page'))
                ->withQueryString(),
            'filters' => $filters,
            'statusLabels' => config('monitoring.workflow_status_labels'),
        ]);
    }

    public function create(): View
    {
        return view('atk.requests.form', ['items' => $this->inventory->options()]);
    }

    public function store(StoreAtkRequestRequest $request)
    {
        $this->requests->submit($request->validated(), $request->validated('items'), $request->user()->id);

        return redirect()->route('atk.requests.index')->with('status', 'Permintaan ATK berhasil diajukan.');
    }

    public function show(AtkRequest $atk_request): View
    {
        $this->authorize('view', $atk_request);

        return view('atk.requests.show', [
            'atkRequest' => $this->requests->find($atk_request->id),
            'statusLabels' => config('monitoring.workflow_status_labels'),
        ]);
    }

    public function act(AtkRequestActionRequest $request, AtkRequest $atk_request)
    {
        $this->requests->act($atk_request, $request->validated('action'), $request->validated('notes'));

        return back()->with('status', 'Permintaan ATK berhasil diproses.');
    }
}
