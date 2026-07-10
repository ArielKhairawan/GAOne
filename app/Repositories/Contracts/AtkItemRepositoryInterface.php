<?php

namespace App\Repositories\Contracts;

use App\Models\AtkItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AtkItemRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): AtkItem;

    public function create(array $data): AtkItem;

    public function update(AtkItem $item, array $data): AtkItem;

    public function delete(AtkItem $item): bool;

    public function all(): Collection;

    public function lowStock(): Collection;

    public function adjustStock(AtkItem $item, int $delta): AtkItem;
}
