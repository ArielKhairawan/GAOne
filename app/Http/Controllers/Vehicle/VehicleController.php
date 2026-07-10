<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\Monitoring\VehicleService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(private VehicleService $vehicles)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'search']);

        // Driver hanya melihat kendaraan miliknya sendiri ("Kendaraan Saya").
        if ($request->user()->hasRole('Driver') && ! $request->user()->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            $filters['driver_id'] = $request->user()->id;
        }

        $vehicles = $this->vehicles->list($filters, (int) config('monitoring.per_page'));

        return view('vehicle.index', [
            'vehicles' => $vehicles,
            'filters' => $filters,
            'stats' => $this->vehicles->dashboardStats(),
        ]);
    }

    public function create(): View
    {
        return view('vehicle.form', ['vehicle' => new Vehicle, 'driverOptions' => $this->driverOptions()]);
    }

    public function store(StoreVehicleRequest $request)
    {
        $this->vehicles->create($request->validated());

        return redirect()->route('vehicle.index')->with('status', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('vehicle.form', ['vehicle' => $vehicle, 'driverOptions' => $this->driverOptions()]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $this->vehicles->update($vehicle, $request->validated());

        return redirect()->route('vehicle.index')->with('status', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->vehicles->delete($vehicle);

        return back()->with('status', 'Kendaraan berhasil dihapus.');
    }

    private function driverOptions()
    {
        return User::role('Driver')->orderBy('name')->get();
    }
}
