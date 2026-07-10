<?php

namespace App\Repositories\Eloquent;

use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VehicleRepository implements VehicleRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Vehicle::query()
            ->status($filters['status'] ?? null)
            ->search($filters['search'] ?? null)
            ->when($filters['driver_id'] ?? null, fn ($q, $v) => $q->ownedBy($v))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Vehicle
    {
        return Vehicle::findOrFail($id);
    }

    public function create(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $vehicle->update($data);

        return $vehicle;
    }

    public function delete(Vehicle $vehicle): bool
    {
        return $vehicle->delete();
    }

    public function active(): Collection
    {
        return Vehicle::query()->where('status', 'aktif')->orderBy('plat_nomor')->get();
    }

    public function countByStatus(): array
    {
        return Vehicle::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }
}
