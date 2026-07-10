<?php

namespace App\Services\Monitoring;

use App\Models\ToiletInspection;
use App\Repositories\Contracts\ToiletInspectionRepositoryInterface;
use App\Services\ToiletNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ToiletInspectionService
{
    public function __construct(
        private ToiletInspectionRepositoryInterface $inspections,
        private ToiletNotificationService $notifier,
    ) {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->inspections->paginate($filters, $perPage);
    }

    public function find(int $id): ToiletInspection
    {
        return $this->inspections->find($id);
    }

    public function create(array $data, array $items, int $userId): ToiletInspection
    {
        $data = $this->storePhotoIfPresent($data);
        $data['created_by'] = $userId;

        $inspection = $this->inspections->create($data, $items);
        $this->notifier->handleAfterSave($inspection);

        return $inspection;
    }

    public function update(ToiletInspection $inspection, array $data, array $items): ToiletInspection
    {
        $data = $this->storePhotoIfPresent($data, $inspection);

        $updated = $this->inspections->update($inspection, $data, $items);
        $this->notifier->handleAfterSave($updated);

        return $updated;
    }

    public function delete(ToiletInspection $inspection): bool
    {
        if ($inspection->foto) {
            Storage::disk('public')->delete($inspection->foto);
        }

        return $this->inspections->delete($inspection);
    }

    public function forExport(array $filters): Collection
    {
        return $this->inspections->forExport($filters);
    }

    public function dashboardStats(): array
    {
        return $this->inspections->todayStats();
    }

    public function activityChart(): Collection
    {
        return $this->inspections->last7DaysActivity();
    }

    public function checkOverdueLocations(): void
    {
        $this->notifier->checkOverdueInspections($this->inspections->latestPerLocation());
    }

    /**
     * Menyimpan file foto ke storage bila ada upload baru. Jika tidak ada
     * upload baru, key 'foto' dihapus dari payload agar nilai lama tidak
     * tertimpa null saat update.
     */
    private function storePhotoIfPresent(array $data, ?ToiletInspection $existing = null): array
    {
        if (! isset($data['foto']) || ! $data['foto'] instanceof UploadedFile) {
            unset($data['foto']);

            return $data;
        }

        if ($existing && $existing->foto) {
            Storage::disk('public')->delete($existing->foto);
        }

        $data['foto'] = $data['foto']->store('toilet-photos', 'public');

        return $data;
    }
}
