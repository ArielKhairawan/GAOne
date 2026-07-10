<?php

namespace App\Exports;

use App\Models\FuelLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FuelLogExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $logs)
    {
    }

    public function collection(): Collection
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
            'Tanggal Pengisian',
            'Plat Kendaraan',
            'Jenis Kendaraan',
            'Driver',
            'Jenis BBM',
            'Harga / Liter',
            'Jumlah Liter',
            'Total Harga',
            'KM Awal',
            'KM Akhir',
            'Jarak Tempuh (km)',
            'Konsumsi (km/liter)',
            'Keterangan',
        ];
    }

    public function map($log): array
    {
        /** @var FuelLog $log */
        return [
            $log->tanggal_pengisian->format('d-m-Y'),
            $log->vehicle->plat_nomor ?? '—',
            $log->vehicle->jenis_kendaraan ?? '—',
            $log->driver_name,
            $log->jenis_bahan_bakar,
            (float) $log->harga_per_liter,
            (float) $log->jumlah_liter,
            (float) $log->total_harga,
            $log->kilometer_awal,
            $log->kilometer_akhir,
            $log->jarak_tempuh,
            $log->konsumsi_bbm,
            $log->keterangan,
        ];
    }
}
