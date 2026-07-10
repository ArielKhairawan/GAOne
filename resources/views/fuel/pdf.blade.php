<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bahan Bakar</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 2px 0; }
        .subtitle { font-size: 10px; color: #6b7280; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; font-size: 10px; text-transform: uppercase; }
        td { font-size: 10.5px; }
        .text-right { text-align: right; }
        .totals { margin-top: 12px; font-size: 11px; }
        .totals td { border: none; padding: 2px 6px; }
    </style>
</head>
<body>
    <h1>Laporan Monitoring Bahan Bakar</h1>
    <div class="subtitle">
        Dibuat pada {{ $generatedAt->format('d M Y H:i') }}
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            &middot; Periode: {{ $filters['date_from'] ?? '...' }} s/d {{ $filters['date_to'] ?? '...' }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Plat Kendaraan</th>
                <th>Driver</th>
                <th>Jenis BBM</th>
                <th class="text-right">Liter</th>
                <th class="text-right">Harga/Liter</th>
                <th class="text-right">Total Harga</th>
                <th class="text-right">Jarak (km)</th>
                <th class="text-right">Konsumsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->tanggal_pengisian->format('d-m-Y') }}</td>
                <td>{{ $log->vehicle->plat_nomor ?? '-' }}</td>
                <td>{{ $log->driver_name ?: '-' }}</td>
                <td>{{ $log->jenis_bahan_bakar }}</td>
                <td class="text-right">{{ number_format($log->jumlah_liter, 1) }}</td>
                <td class="text-right">{{ number_format($log->harga_per_liter, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($log->total_harga, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($log->jarak_tempuh) }}</td>
                <td class="text-right">{{ $log->konsumsi_bbm ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center; padding:16px;">Tidak ada data untuk periode/filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td><strong>Total Liter</strong></td>
            <td>: {{ number_format($logs->sum('jumlah_liter'), 1) }}</td>
        </tr>
        <tr>
            <td><strong>Total Pengeluaran</strong></td>
            <td>: Rp {{ number_format($logs->sum('total_harga'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Jumlah Pengisian</strong></td>
            <td>: {{ $logs->count() }}</td>
        </tr>
    </table>
</body>
</html>
