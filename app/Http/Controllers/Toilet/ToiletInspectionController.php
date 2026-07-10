<?php

namespace App\Http\Controllers\Toilet;

use App\Exports\ToiletInspectionExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Toilet\StoreToiletInspectionRequest;
use App\Http\Requests\Toilet\UpdateToiletInspectionRequest;
use App\Models\ToiletInspection;
use App\Models\User;
use App\Services\Monitoring\ToiletInspectionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ToiletInspectionController extends Controller
{
    public function __construct(private ToiletInspectionService $inspections)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['lokasi', 'petugas', 'status', 'date_from', 'date_to']);

        // Petugas Kebersihan secara default melihat riwayat inspeksinya sendiri
        // di dashboard, namun di halaman daftar utama tetap melihat semua data
        // (untuk koordinasi antar petugas) kecuali ingin difilter manual.
        $inspections = $this->inspections->list($filters, (int) config('monitoring.per_page'));

        return view('toilet.index', [
            'inspections' => $inspections,
            'filters' => $filters,
            'stats' => $this->inspections->dashboardStats(),
            'locations' => config('monitoring.toilet_locations'),
            'statuses' => config('monitoring.toilet_statuses'),
        ]);
    }

    public function create(): View
    {
        return view('toilet.form', [
            'inspection' => new ToiletInspection,
            'checklistItems' => config('monitoring.toilet_checklist_items'),
            'itemStatuses' => config('monitoring.toilet_checklist_item_statuses'),
            'locations' => config('monitoring.toilet_locations'),
            'statuses' => config('monitoring.toilet_statuses'),
            'petugasOptions' => $this->petugasOptions(),
        ]);
    }

    public function store(StoreToiletInspectionRequest $request)
    {
        $data = $request->safe()->except('items');
        $items = $request->validated('items');

        $this->inspections->create($data, $items, $request->user()->id);

        return redirect()->route('toilet.index')->with('status', 'Data inspeksi WC berhasil disimpan.');
    }

    public function show(ToiletInspection $toiletInspection): View
    {
        return view('toilet.show', ['inspection' => $toiletInspection->load(['items', 'petugasUser'])]);
    }

    public function edit(ToiletInspection $toiletInspection): View
    {
        $this->authorize('update', $toiletInspection);

        return view('toilet.form', [
            'inspection' => $toiletInspection->load('items'),
            'checklistItems' => config('monitoring.toilet_checklist_items'),
            'itemStatuses' => config('monitoring.toilet_checklist_item_statuses'),
            'locations' => config('monitoring.toilet_locations'),
            'statuses' => config('monitoring.toilet_statuses'),
            'petugasOptions' => $this->petugasOptions(),
        ]);
    }

    public function update(UpdateToiletInspectionRequest $request, ToiletInspection $toiletInspection)
    {
        $this->authorize('update', $toiletInspection);

        $data = $request->safe()->except('items');
        $items = $request->validated('items');

        $this->inspections->update($toiletInspection, $data, $items);

        return redirect()->route('toilet.index')->with('status', 'Data inspeksi WC berhasil diperbarui.');
    }

    public function destroy(ToiletInspection $toiletInspection)
    {
        $this->inspections->delete($toiletInspection);

        return back()->with('status', 'Data inspeksi WC berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only(['lokasi', 'petugas', 'status', 'date_from', 'date_to']);
        $inspections = $this->inspections->forExport($filters);

        return Pdf::loadView('toilet.pdf', ['inspections' => $inspections, 'filters' => $filters, 'generatedAt' => now()])
            ->download('laporan-kebersihan-wc-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['lokasi', 'petugas', 'status', 'date_from', 'date_to']);
        $inspections = $this->inspections->forExport($filters);

        return Excel::download(new ToiletInspectionExport($inspections), 'laporan-kebersihan-wc-'.now()->format('Ymd-His').'.xlsx');
    }

    private function petugasOptions()
    {
        return User::role(['Petugas Kebersihan', 'GA Staff', 'Admin'])->orderBy('name')->get();
    }
}
