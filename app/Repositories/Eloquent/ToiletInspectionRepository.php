<?php

namespace App\Repositories\Eloquent;

use App\Models\ToiletInspection;
use App\Repositories\Contracts\ToiletInspectionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ToiletInspectionRepository implements ToiletInspectionRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return ToiletInspection::query()
            ->with('items')
            ->filter($filters)
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): ToiletInspection
    {
        return ToiletInspection::with('items')->findOrFail($id);
    }

    public function create(array $data, array $items): ToiletInspection
    {
        return DB::transaction(function () use ($data, $items) {
            $inspection = ToiletInspection::create($data);
            $this->syncItems($inspection, $items);

            return $inspection->load('items');
        });
    }

    public function update(ToiletInspection $inspection, array $data, array $items): ToiletInspection
    {
        return DB::transaction(function () use ($inspection, $data, $items) {
            $inspection->update($data);
            $inspection->items()->delete();
            $this->syncItems($inspection, $items);

            return $inspection->load('items');
        });
    }

    public function delete(ToiletInspection $inspection): bool
    {
        return $inspection->delete();
    }

    public function forExport(array $filters = []): Collection
    {
        return ToiletInspection::query()
            ->with('items')
            ->filter($filters)
            ->orderBy('tanggal')
            ->get();
    }

    public function todayStats(): array
    {
        $today = ToiletInspection::query()->whereDate('tanggal', today())->get();

        return [
            'total_hari_ini' => $today->count(),
            'bersih' => $today->where('status', 'bersih')->count(),
            'perlu_tindakan' => $today->whereIn('status', ['kurang_bersih', 'kotor'])->count(),
        ];
    }

    public function last7DaysActivity(): Collection
    {
        $start = today()->subDays(6);

        $rows = ToiletInspection::query()
            ->where('tanggal', '>=', $start->toDateString())
            ->get(['tanggal', 'status']);

        $grouped = $rows->groupBy(fn (ToiletInspection $row) => Carbon::parse($row->tanggal)->format('Y-m-d'));

        $series = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day = today()->subDays($i);
            $key = $day->format('Y-m-d');
            $dayRows = $grouped->get($key, collect());

            $series->push([
                'label' => $day->translatedFormat('d M'),
                'total' => $dayRows->count(),
                'kotor' => $dayRows->where('status', 'kotor')->count(),
            ]);
        }

        return $series;
    }

    public function latestPerLocation(): Collection
    {
        $locations = config('monitoring.toilet_locations', []);

        return collect($locations)->map(function (string $lokasi) {
            $latest = ToiletInspection::query()
                ->where('lokasi', $lokasi)
                ->orderByDesc('tanggal')
                ->orderByDesc('jam')
                ->first();

            return [
                'lokasi' => $lokasi,
                'inspection' => $latest,
            ];
        });
    }

    private function syncItems(ToiletInspection $inspection, array $items): void
    {
        foreach ($items as $itemName => $status) {
            $inspection->items()->create([
                'item_name' => $itemName,
                'status' => $status,
            ]);
        }
    }
}
