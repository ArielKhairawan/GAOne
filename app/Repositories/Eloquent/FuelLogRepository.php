<?php

namespace App\Repositories\Eloquent;

use App\Models\FuelLog;
use App\Repositories\Contracts\FuelLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FuelLogRepository implements FuelLogRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return FuelLog::query()
            ->with('vehicle')
            ->filter($filters)
            ->orderByDesc('tanggal_pengisian')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): FuelLog
    {
        return FuelLog::with('vehicle')->findOrFail($id);
    }

    public function create(array $data): FuelLog
    {
        return FuelLog::create($data);
    }

    public function update(FuelLog $fuelLog, array $data): FuelLog
    {
        $fuelLog->update($data);

        return $fuelLog;
    }

    public function delete(FuelLog $fuelLog): bool
    {
        return $fuelLog->delete();
    }

    public function forExport(array $filters = []): Collection
    {
        return FuelLog::query()
            ->with('vehicle')
            ->filter($filters)
            ->orderBy('tanggal_pengisian')
            ->get();
    }

    public function monthlyTotals(?string $yearMonth = null): array
    {
        $month = $yearMonth ? Carbon::parse($yearMonth.'-01') : now();
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $rows = FuelLog::query()->whereBetween('tanggal_pengisian', [$start, $end])->get();

        return [
            'total_pengeluaran' => (float) $rows->sum('total_harga'),
            'total_liter' => (float) $rows->sum('jumlah_liter'),
            'total_pengisian' => $rows->count(),
        ];
    }

    public function averageConsumption(): float
    {
        return (float) (FuelLog::query()->whereNotNull('konsumsi_bbm')->avg('konsumsi_bbm') ?: 0);
    }

    public function monthlySpendingSeries(int $months = 6): Collection
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $rows = FuelLog::query()
            ->where('tanggal_pengisian', '>=', $start->toDateString())
            ->get(['tanggal_pengisian', 'total_harga']);

        $grouped = $rows->groupBy(fn (FuelLog $row) => Carbon::parse($row->tanggal_pengisian)->format('Y-m'))
            ->map(fn (Collection $group) => (float) $group->sum('total_harga'));

        $series = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $series->push([
                'label' => $month->translatedFormat('M Y'),
                'total' => $grouped->get($key, 0),
            ]);
        }

        return $series;
    }

    public function consumptionPerVehicle(int $limit = 8): Collection
    {
        return FuelLog::query()
            ->with('vehicle')
            ->selectRaw('vehicle_id, avg(konsumsi_bbm) as avg_konsumsi')
            ->whereNotNull('konsumsi_bbm')
            ->groupBy('vehicle_id')
            ->orderByDesc('avg_konsumsi')
            ->limit($limit)
            ->get()
            ->map(fn (FuelLog $row) => [
                'plat_nomor' => $row->vehicle->plat_nomor ?? '—',
                'avg_konsumsi' => round((float) $row->avg_konsumsi, 2),
            ]);
    }
}
