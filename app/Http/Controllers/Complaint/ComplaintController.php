<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\Controller;
use App\Http\Requests\Complaint\StoreComplaintRequest;
use App\Models\Complaint;
use App\Services\Meeting\ComplaintService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function __construct(private ComplaintService $complaints)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['status']);

        if ($request->user()->hasRole('Karyawan') && ! $request->user()->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            $filters['user_id'] = $request->user()->id;
        }

        return view('complaint.index', [
            'complaints' => Complaint::query()
                ->with(['user', 'resolver'])
                ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
                ->filter($filters)
                ->latest()
                ->paginate((int) config('monitoring.per_page'))
                ->withQueryString(),
            'filters' => $filters,
            'statusLabels' => config('monitoring.complaint_statuses'),
        ]);
    }

    public function create(): View
    {
        return view('complaint.form');
    }

    public function store(StoreComplaintRequest $request)
    {
        $this->complaints->create($request->validated(), $request->user()->id);

        return redirect()->route('complaint.index')->with('status', 'Pengaduan berhasil dikirim.');
    }

    public function show(Complaint $complaint): View
    {
        $this->authorize('view', $complaint);

        return view('complaint.show', [
            'complaintItem' => $this->complaints->find($complaint->id),
            'statusLabels' => config('monitoring.complaint_statuses'),
        ]);
    }

    public function markProcessing(Complaint $complaint)
    {
        $this->complaints->markProcessing($complaint);

        return back()->with('status', 'Pengaduan ditandai sedang diproses.');
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $this->complaints->resolve($complaint, $request->user()->id);

        return back()->with('status', 'Pengaduan ditandai selesai.');
    }
}
