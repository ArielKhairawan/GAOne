<?php

namespace App\Repositories\Contracts;

use App\Models\FuelLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FuelLogRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): FuelLog;

    public function create(array $data): FuelLog;

    public function update(FuelLog $fuelLog, array $data): FuelLog;

    public function delete(FuelLog $fuelLog): bool;

    public function forExport(array $filters = []): Collection;

    public function monthlyTotals(?string $yearMonth = null): array;

    public function averageConsumption(): float;

    public function monthlySpendingSeries(int $months = 6): Collection;

    public function consumptionPerVehicle(int $limit = 8): Collection;
}
