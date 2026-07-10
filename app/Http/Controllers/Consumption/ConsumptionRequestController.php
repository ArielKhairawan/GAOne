<?php

namespace App\Http\Controllers\Consumption;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumption\ConsumptionActionRequest;
use App\Http\Requests\Consumption\StoreConsumptionRequest;
use App\Models\ConsumptionRequest;
use App\Services\Meeting\ConsumptionRequestService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsumptionRequestController extends Controller
{
    public function __construct(private ConsumptionRequestService $requests)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'date_from', 'date_to']);

        if ($request->user()->hasRole('Karyawan') && ! $request->user()->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            $filters['user_id'] = $request->user()->id;
        }

        return view('consumption.index', [
            'requests' => ConsumptionRequest::query()
                ->with(['requester', 'meetingBooking'])
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
        return view('consumption.form');
    }

    public function store(StoreConsumptionRequest $request)
    {
        $this->requests->submit($request->validated(), $request->user()->id);

        return redirect()->route('consumption.index')->with('status', 'Permintaan konsumsi berhasil diajukan.');
    }

    public function show(ConsumptionRequest $consumption_request): View
    {
        $this->authorize('view', $consumption_request);

        return view('consumption.show', [
            'consumptionRequest' => $this->requests->find($consumption_request->id),
            'statusLabels' => config('monitoring.workflow_status_labels'),
        ]);
    }

    public function act(ConsumptionActionRequest $request, ConsumptionRequest $consumption_request)
    {
        $this->requests->act($consumption_request, $request->validated('action'), $request->validated('notes'));

        return back()->with('status', 'Permintaan konsumsi berhasil diproses.');
    }

    public function markProcessing(ConsumptionRequest $consumption_request)
    {
        $this->requests->markProcessing($consumption_request);

        return back()->with('status', 'Status diubah menjadi Diproses.');
    }

    public function complete(ConsumptionRequest $consumption_request)
    {
        $this->requests->markCompleted($consumption_request);

        return back()->with('status', 'Permintaan konsumsi ditandai selesai.');
    }
}
