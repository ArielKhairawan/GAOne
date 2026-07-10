<div class="row g-4 mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--amber)">
            <div class="metric-card-accent" style="background: var(--amber)"></div>
            <div class="metric-top"><span class="metric-label">Pengisian BBM Hari Ini</span></div>
            <div class="metric-value">{{ $data['aktivitas_hari_ini']['fuel'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--emerald)">
            <div class="metric-card-accent" style="background: var(--emerald)"></div>
            <div class="metric-top"><span class="metric-label">Inspeksi WC Hari Ini</span></div>
            <div class="metric-value">{{ $data['aktivitas_hari_ini']['toilet'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--sky)">
            <div class="metric-card-accent" style="background: var(--sky)"></div>
            <div class="metric-top"><span class="metric-label">Permintaan ATK Hari Ini</span></div>
            <div class="metric-value">{{ $data['aktivitas_hari_ini']['atk'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--crimson)">
            <div class="metric-card-accent" style="background: var(--crimson)"></div>
            <div class="metric-top"><span class="metric-label">Booking Meeting Hari Ini</span></div>
            <div class="metric-value">{{ $data['aktivitas_hari_ini']['meeting'] }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card mb-4">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Kendaraan Perlu Servis</span></div>
            <div class="card-body p-0">
                @forelse($data['kendaraan_servis'] as $v)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border)"><span style="font-size:13.5px">{{ $v->plat_nomor }} — {{ $v->jenis_kendaraan }}</span></div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Tidak ada kendaraan yang perlu servis.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Pengisian BBM Terbaru</span></div>
            <div class="card-body p-0">
                @forelse($data['fuel_terbaru'] as $f)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $f->vehicle->plat_nomor ?? '—' }}</span>
                    <span style="font-size:12px; color:var(--text-3)">Rp {{ number_format($f->total_harga, 0, ',', '.') }}</span>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Belum ada data.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Inspeksi WC Terbaru</span></div>
            <div class="card-body p-0">
                @forelse($data['toilet_terbaru'] as $t)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $t->lokasi_detail ?: $t->lokasi }}</span>
                    <span class="status-badge {{ $t->status === 'bersih' ? 'active' : ($t->status === 'kotor' ? 'inactive' : 'pending') }}">{{ config('monitoring.toilet_statuses')[$t->status] }}</span>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Belum ada data.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Permintaan ATK & Booking Meeting Terbaru</span></div>
            <div class="card-body p-0">
                @foreach($data['atk_terbaru'] as $r)
                <div style="padding:10px 20px; border-bottom:1px solid var(--border)"><span style="font-size:13px">📦 {{ $r->requester->name ?? '—' }} — {{ $r->department }}</span></div>
                @endforeach
                @foreach($data['meeting_terbaru'] as $b)
                <div style="padding:10px 20px; border-bottom:1px solid var(--border)"><span style="font-size:13px">📅 {{ $b->nama_kegiatan }} — {{ $b->room->nama_ruangan ?? '—' }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@include('dashboards.partials.notifications', ['notifications' => $data['notifications']])
