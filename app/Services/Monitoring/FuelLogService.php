<?php

namespace App\Services\Monitoring;

use App\Models\FuelLog;
use App\Repositories\Contracts\FuelLogRepositoryInterface;
use App\Services\FuelNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FuelLogService
{
    public function __construct(
        private FuelLogRepositoryInterface $fuelLogs,
        private FuelNotificationService $notifier,
    ) {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->fuelLogs->paginate($filters, $perPage);
    }

    public function find(int $id): FuelLog
    {
        return $this->fuelLogs->find($id);
    }

    public function create(array $data, int $userId): FuelLog
    {
        $data = $this->withComputedFields($data);
        $data['created_by'] = $userId;

        $fuelLog = $this->fuelLogs->create($data);
        $this->notifier->handleAfterSave($fuelLog);

        return $fuelLog;
    }

    public function update(FuelLog $fuelLog, array $data): FuelLog
    {
        $data = $this->withComputedFields($data);

        $updated = $this->fuelLogs->update($fuelLog, $data);
        $this->notifier->handleAfterSave($updated);

        return $updated;
    }

    public function delete(FuelLog $fuelLog): bool
    {
        return $this->fuelLogs->delete($fuelLog);
    }

    public function forExport(array $filters): Collection
    {
        return $this->fuelLogs->forExport($filters);
    }

    public function dashboardStats(): array
    {
        $totals = $this->fuelLogs->monthlyTotals();

        return [
            'total_pengeluaran_bulan_ini' => $totals['total_pengeluaran'],
            'total_liter_bulan_ini' => $totals['total_liter'],
            'total_pengisian_bulan_ini' => $totals['total_pengisian'],
            'rata_rata_konsumsi' => round($this->fuelLogs->averageConsumption(), 2),
        ];
    }

    public function monthlySpendingChart(int $months = 6): Collection
    {
        return $this->fuelLogs->monthlySpendingSeries($months);
    }

    public function consumptionPerVehicleChart(int $limit = 8): Collection
    {
        return $this->fuelLogs->consumptionPerVehicle($limit);
    }

    /**
     * Menghitung jarak tempuh, konsumsi BBM (km/liter), dan total harga
     * secara konsisten di satu tempat, supaya tidak ada logika perhitungan
     * yang terduplikasi di controller maupun blade view.
     */
    private function withComputedFields(array $data): array
    {
        $jarakTempuh = max(0, (int) $data['kilometer_akhir'] - (int) $data['kilometer_awal']);
        $jumlahLiter = (float) $data['jumlah_liter'];

        $data['jarak_tempuh'] = $jarakTempuh;
        $data['konsumsi_bbm'] = $jumlahLiter > 0 ? round($jarakTempuh / $jumlahLiter, 2) : null;
        $data['total_harga'] = round(((float) $data['harga_per_liter']) * $jumlahLiter, 2);

        return $data;
    }
}
