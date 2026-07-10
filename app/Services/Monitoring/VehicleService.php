<?php

namespace App\Services\Monitoring;

use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Services\VehicleNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VehicleService
{
    public function __construct(
        private VehicleRepositoryInterface $vehicles,
        private VehicleNotificationService $notifier,
    ) {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->vehicles->paginate($filters, $perPage);
    }

    public function find(int $id): Vehicle
    {
        return $this->vehicles->find($id);
    }

    public function create(array $data): Vehicle
    {
        return $this->vehicles->create($data);
    }

    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $previousStatus = $vehicle->status;
        $updated = $this->vehicles->update($vehicle, $data);

        $this->notifier->handleStatusChange($updated, $previousStatus);

        return $updated;
    }

    public function delete(Vehicle $vehicle): bool
    {
        return $this->vehicles->delete($vehicle);
    }

    public function activeOptions(): Collection
    {
        return $this->vehicles->active();
    }

    public function dashboardStats(): array
    {
        $byStatus = $this->vehicles->countByStatus();

        return [
            'total_unit' => array_sum($byStatus),
            'unit_aktif' => $byStatus['aktif'] ?? 0,
            'unit_servis' => $byStatus['servis'] ?? 0,
            'unit_tidak_aktif' => $byStatus['tidak_aktif'] ?? 0,
            'by_status' => $byStatus,
        ];
    }
}
