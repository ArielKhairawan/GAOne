<?php

namespace App\Http\Controllers\Fuel;

use App\Exports\FuelLogExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fuel\StoreFuelLogRequest;
use App\Http\Requests\Fuel\UpdateFuelLogRequest;
use App\Models\FuelLog;
use App\Models\User;
use App\Services\Monitoring\FuelLogService;
use App\Services\Monitoring\VehicleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class FuelLogController extends Controller
{
    public function __construct(
        private FuelLogService $fuelLogs,
        private VehicleService $vehicles,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'driver', 'plat_nomor', 'jenis_bahan_bakar']);

        // Driver hanya melihat riwayat pengisian BBM miliknya sendiri.
        if ($request->user()->hasRole('Driver') && ! $request->user()->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            $filters['owner_id'] = $request->user()->id;
        }

        $logs = $this->fuelLogs->list($filters, (int) config('monitoring.per_page'));

        return view('fuel.index', [
            'logs' => $logs,
            'filters' => $filters,
            'stats' => $this->fuelLogs->dashboardStats(),
            'monthlyChart' => $this->fuelLogs->monthlySpendingChart(6),
            'consumptionChart' => $this->fuelLogs->consumptionPerVehicleChart(),
            'fuelTypes' => config('monitoring.fuel_types'),
        ]);
    }

    public function create(): View
    {
        return view('fuel.form', [
            'fuelLog' => new FuelLog,
            'vehicles' => $this->vehicles->activeOptions(),
            'fuelTypes' => config('monitoring.fuel_types'),
            'driverOptions' => $this->driverOptions(),
        ]);
    }

    public function store(StoreFuelLogRequest $request)
    {
        $this->fuelLogs->create($request->validated(), $request->user()->id);

        return redirect()->route('fuel.index')->with('status', 'Data pengisian BBM berhasil disimpan.');
    }

    public function edit(FuelLog $fuelLog): View
    {
        $this->authorize('update', $fuelLog);

        return view('fuel.form', [
            'fuelLog' => $fuelLog,
            'vehicles' => $this->vehicles->activeOptions(),
            'fuelTypes' => config('monitoring.fuel_types'),
            'driverOptions' => $this->driverOptions(),
        ]);
    }

    public function update(UpdateFuelLogRequest $request, FuelLog $fuelLog)
    {
        $this->fuelLogs->update($fuelLog, $request->validated());

        return redirect()->route('fuel.index')->with('status', 'Data pengisian BBM berhasil diperbarui.');
    }

    public function destroy(FuelLog $fuelLog)
    {
        $this->fuelLogs->delete($fuelLog);

        return back()->with('status', 'Data pengisian BBM berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'driver', 'plat_nomor', 'jenis_bahan_bakar']);
        $logs = $this->fuelLogs->forExport($filters);

        return Pdf::loadView('fuel.pdf', ['logs' => $logs, 'filters' => $filters, 'generatedAt' => now()])
            ->download('laporan-bbm-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'driver', 'plat_nomor', 'jenis_bahan_bakar']);
        $logs = $this->fuelLogs->forExport($filters);

        return Excel::download(new FuelLogExport($logs), 'laporan-bbm-'.now()->format('Ymd-His').'.xlsx');
    }

    private function driverOptions()
    {
        return User::role('Driver')->orderBy('name')->get();
    }
}
