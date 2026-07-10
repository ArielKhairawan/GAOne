<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kebersihan WC</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 2px 0; }
        .subtitle { font-size: 10px; color: #6b7280; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; font-size: 10px; text-transform: uppercase; }
        td { font-size: 10.5px; }
    </style>
</head>
<body>
    <h1>Laporan Monitoring Kebersihan WC</h1>
    <div class="subtitle">
        Dibuat pada {{ $generatedAt->format('d M Y H:i') }}
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            &middot; Periode: {{ $filters['date_from'] ?? '...' }} s/d {{ $filters['date_to'] ?? '...' }}
        @endif
        @if(!empty($filters['lokasi']))
            &middot; Lokasi: {{ $filters['lokasi'] }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Lokasi</th>
                <th>Petugas</th>
                <th>Status</th>
                <th>Item Bermasalah</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspections as $inspection)
            <tr>
                <td>{{ $inspection->tanggal->format('d-m-Y') }}</td>
                <td>{{ $inspection->jam }}</td>
                <td>{{ $inspection->lokasi_detail ?: $inspection->lokasi }}</td>
                <td>{{ $inspection->petugas_name ?: '-' }}</td>
                <td>{{ config('monitoring.toilet_statuses')[$inspection->status] ?? $inspection->status }}</td>
                <td>{{ $inspection->items->whereIn('status', ['kurang', 'rusak'])->pluck('item_name')->implode(', ') ?: '-' }}</td>
                <td>{{ $inspection->catatan ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; padding:16px;">Tidak ada data untuk periode/filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
