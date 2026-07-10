<?php

namespace App\Exports;

use App\Models\AtkItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AtkItemExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $items)
    {
    }

    public function collection(): Collection
    {
        return $this->items;
    }

    public function headings(): array
    {
        return ['Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Stok', 'Stok Minimum', 'Lokasi Penyimpanan', 'Status'];
    }

    public function map($item): array
    {
        /** @var AtkItem $item */
        return [
            $item->code,
            $item->name,
            $item->category->name ?? '—',
            $item->satuan,
            $item->stock,
            $item->minimum_stock,
            $item->lokasi_penyimpanan,
            config('monitoring.atk_item_statuses')[$item->status],
        ];
    }
}
