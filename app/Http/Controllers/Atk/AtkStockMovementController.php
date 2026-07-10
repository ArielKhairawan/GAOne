<?php

namespace App\Http\Controllers\Atk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Atk\StockMovementRequest;
use App\Models\AtkItem;
use App\Models\AtkStockMovement;
use App\Services\Inventory\AtkInventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AtkStockMovementController extends Controller
{
    public function __construct(private AtkInventoryService $inventory)
    {
    }

    public function indexIn(Request $request): View
    {
        return $this->history('masuk', 'atk.stock-in.index');
    }

    public function indexOut(Request $request): View
    {
        return $this->history('keluar', 'atk.stock-out.index');
    }

    private function history(string $type, string $view): View
    {
        $movements = AtkStockMovement::query()
            ->with(['item', 'user'])
            ->ofType($type)
            ->latest()
            ->paginate((int) config('monitoring.per_page'));

        return view($view, ['movements' => $movements, 'items' => $this->inventory->options()]);
    }

    public function createIn(): View
    {
        return view('atk.movements.form', ['type' => 'masuk', 'items' => $this->inventory->options()]);
    }

    public function storeIn(StockMovementRequest $request)
    {
        $item = AtkItem::findOrFail($request->validated('atk_item_id'));
        $this->inventory->stockIn($item, (int) $request->validated('quantity'), $request->validated('notes'), $request->user()->id);

        return redirect()->route('atk.stock-in.index')->with('status', 'Barang masuk berhasil dicatat.');
    }

    public function createOut(): View
    {
        return view('atk.movements.form', ['type' => 'keluar', 'items' => $this->inventory->options()]);
    }

    public function storeOut(StockMovementRequest $request)
    {
        $item = AtkItem::findOrFail($request->validated('atk_item_id'));
        $this->inventory->stockOut($item, (int) $request->validated('quantity'), $request->validated('notes'), $request->user()->id);

        return redirect()->route('atk.stock-out.index')->with('status', 'Barang keluar berhasil dicatat.');
    }
}
