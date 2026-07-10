<?php

namespace App\Exports;

use App\Models\ToiletInspection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ToiletInspectionExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $inspections)
    {
    }

    public function collection(): Collection
    {
        return $this->inspections;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam',
            'Lokasi',
            'Petugas',
            'Status',
            'Item Bermasalah',
            'Catatan',
        ];
    }

    public function map($inspection): array
    {
        /** @var ToiletInspection $inspection */
        $bermasalah = $inspection->items
            ->whereIn('status', ['kurang', 'rusak'])
            ->pluck('item_name')
            ->implode(', ');

        return [
            $inspection->tanggal->format('d-m-Y'),
            $inspection->jam,
            $inspection->lokasi_detail ?: $inspection->lokasi,
            $inspection->petugas_name,
            config('monitoring.toilet_statuses')[$inspection->status] ?? $inspection->status,
            $bermasalah ?: '—',
            $inspection->catatan,
        ];
    }
}
