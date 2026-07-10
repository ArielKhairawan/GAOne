<div class="row g-4 mb-2">
    <div class="col-xl-4">
        <div class="metric-card project-card" style="border-left-color: var(--sky)">
            <div class="metric-card-accent" style="background: var(--sky)"></div>
            <div class="metric-top"><span class="metric-label">Total Inspeksi Hari Ini</span></div>
            <div class="metric-value">{{ $data['today']['total_hari_ini'] }}</div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="metric-card project-card" style="border-left-color: var(--emerald)">
            <div class="metric-card-accent" style="background: var(--emerald)"></div>
            <div class="metric-top"><span class="metric-label">WC Bersih</span></div>
            <div class="metric-value">{{ $data['today']['bersih'] }}</div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="metric-card project-card" style="border-left-color: var(--crimson)">
            <div class="metric-card-accent" style="background: var(--crimson)"></div>
            <div class="metric-top"><span class="metric-label">Perlu Tindakan</span></div>
            <div class="metric-value">{{ $data['today']['perlu_tindakan'] }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card mb-4">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">WC Belum Diperiksa Hari Ini</span></div>
            <div class="card-body p-0">
                @forelse($data['belum_diperiksa'] as $lokasi)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border)"><span style="font-size:13.5px">⏳ {{ $lokasi }}</span></div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Semua lokasi sudah diperiksa hari ini. 🎉</div>
                @endforelse
            </div>
        </div>

        @can('toilet.create')
        <a href="{{ route('toilet.create') }}" class="btn btn-primary" style="width:100%; justify-content:center">+ Catat Inspeksi Baru</a>
        @endcan
    </div>

    <div class="col-md-6">
        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Temuan Terbaru (Kurang Bersih / Kotor)</span></div>
            <div class="card-body p-0">
                @forelse($data['temuan_terbaru'] as $t)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $t->lokasi_detail ?: $t->lokasi }} &middot; {{ $t->tanggal->format('d M') }}</span>
                    <a href="{{ route('toilet.show', $t) }}" class="small">Lihat</a>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Tidak ada temuan terbaru.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Riwayat Inspeksi Saya</span></div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead><tr><th>Tanggal</th><th>Lokasi</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($data['riwayat_saya'] as $t)
                <tr>
                    <td>{{ $t->tanggal->format('d M Y') }}</td>
                    <td>{{ $t->lokasi_detail ?: $t->lokasi }}</td>
                    <td><span class="status-badge {{ $t->status === 'bersih' ? 'active' : ($t->status === 'kotor' ? 'inactive' : 'pending') }}">{{ config('monitoring.toilet_statuses')[$t->status] }}</span></td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center; padding:32px; color:var(--text-3)">Belum ada riwayat inspeksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('dashboards.partials.notifications', ['notifications' => $data['notifications']])
