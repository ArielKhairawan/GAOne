<div class="row g-4 mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--amber)">
            <div class="metric-card-accent" style="background: var(--amber)"></div>
            <div class="metric-top"><span class="metric-label">Pengeluaran BBM</span><span class="metric-dot" style="background: var(--amber)"></span></div>
            <div class="metric-value" style="font-size:20px">Rp {{ number_format($data['pengeluaran_bbm'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--sky)">
            <div class="metric-card-accent" style="background: var(--sky)"></div>
            <div class="metric-top"><span class="metric-label">Pengeluaran PO</span><span class="metric-dot" style="background: var(--sky)"></span></div>
            <div class="metric-value" style="font-size:20px">Rp {{ number_format($data['rekap']['po'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--emerald)">
            <div class="metric-card-accent" style="background: var(--emerald)"></div>
            <div class="metric-top"><span class="metric-label">Pengeluaran Travel</span><span class="metric-dot" style="background: var(--emerald)"></span></div>
            <div class="metric-value" style="font-size:20px">Rp {{ number_format($data['rekap']['travel'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--crimson)">
            <div class="metric-card-accent" style="background: var(--crimson)"></div>
            <div class="metric-top"><span class="metric-label">Permintaan Konsumsi Selesai</span><span class="metric-dot" style="background: var(--crimson)"></span></div>
            <div class="metric-value">{{ $data['permintaan_konsumsi_selesai'] }}</div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Rekap Biaya Operasional</span></div>
    <div class="card-body p-4">
        <table class="table mb-0">
            <tbody>
                <tr><td>Bahan Bakar (BBM)</td><td class="text-end fw-medium">Rp {{ number_format($data['rekap']['bbm'], 0, ',', '.') }}</td></tr>
                <tr><td>Purchase Order</td><td class="text-end fw-medium">Rp {{ number_format($data['rekap']['po'], 0, ',', '.') }}</td></tr>
                <tr><td>Travel</td><td class="text-end fw-medium">Rp {{ number_format($data['rekap']['travel'], 0, ',', '.') }}</td></tr>
            </tbody>
        </table>
        <p class="small text-muted mb-0">Catatan: ATK belum memiliki harga satuan di skema saat ini, sehingga belum dapat dimasukkan ke rekap nominal (lihat REFACTOR_NOTES.md).</p>
    </div>
</div>

@include('dashboards.partials.notifications', ['notifications' => $data['notifications']])
