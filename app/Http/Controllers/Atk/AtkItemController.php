<?php

namespace App\Http\Controllers\Atk;

use App\Exports\AtkItemExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Atk\StoreAtkItemRequest;
use App\Http\Requests\Atk\UpdateAtkItemRequest;
use App\Models\AtkCategory;
use App\Models\AtkItem;
use App\Services\Inventory\AtkInventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AtkItemController extends Controller
{
    public function __construct(private AtkInventoryService $inventory)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'atk_category_id']);
        $items = $this->inventory->list($filters, (int) config('monitoring.per_page'));

        return view('atk.items.index', [
            'items' => $items,
            'filters' => $filters,
            'categories' => AtkCategory::orderBy('name')->get(),
            'lowStockCount' => $this->inventory->lowStockItems()->count(),
        ]);
    }

    public function create(): View
    {
        return view('atk.items.form', ['item' => new AtkItem, 'categories' => AtkCategory::orderBy('name')->get()]);
    }

    public function store(StoreAtkItemRequest $request)
    {
        $this->inventory->create($request->validated());

        return redirect()->route('atk.items.index')->with('status', 'Barang ATK berhasil ditambahkan.');
    }

    public function edit(AtkItem $atk_item): View
    {
        return view('atk.items.form', ['item' => $atk_item, 'categories' => AtkCategory::orderBy('name')->get()]);
    }

    public function update(UpdateAtkItemRequest $request, AtkItem $atk_item)
    {
        $this->inventory->update($atk_item, $request->validated());

        return redirect()->route('atk.items.index')->with('status', 'Barang ATK berhasil diperbarui.');
    }

    public function destroy(AtkItem $atk_item)
    {
        $this->inventory->delete($atk_item);

        return back()->with('status', 'Barang ATK berhasil dihapus.');
    }

    public function exportPdf()
    {
        $items = $this->inventory->options();

        return Pdf::loadView('atk.pdf', ['items' => $items, 'generatedAt' => now()])
            ->download('laporan-atk-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel()
    {
        $items = $this->inventory->options();

        return Excel::download(new AtkItemExport($items), 'laporan-atk-'.now()->format('Ymd-His').'.xlsx');
    }
}
