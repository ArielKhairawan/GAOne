<?php

namespace App\Repositories\Contracts;

use App\Models\ToiletInspection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ToiletInspectionRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ToiletInspection;

    public function create(array $data, array $items): ToiletInspection;

    public function update(ToiletInspection $inspection, array $data, array $items): ToiletInspection;

    public function delete(ToiletInspection $inspection): bool;

    public function forExport(array $filters = []): Collection;

    public function todayStats(): array;

    public function last7DaysActivity(): Collection;

    public function latestPerLocation(): Collection;
}
