<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Inventaris ATK</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 2px 0; }
        .subtitle { font-size: 10px; color: #6b7280; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; font-size: 10px; text-transform: uppercase; }
        td { font-size: 10.5px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Inventaris ATK</h1>
    <div class="subtitle">Dibuat pada {{ $generatedAt->format('d M Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th>
                <th class="text-right">Stok</th><th class="text-right">Min. Stok</th><th>Lokasi</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ $item->code }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? '-' }}</td>
                <td>{{ $item->satuan }}</td>
                <td class="text-right">{{ $item->stock }}</td>
                <td class="text-right">{{ $item->minimum_stock }}</td>
                <td>{{ $item->lokasi_penyimpanan ?: '-' }}</td>
                <td>{{ config('monitoring.atk_item_statuses')[$item->status] }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; padding:16px;">Tidak ada data barang.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
