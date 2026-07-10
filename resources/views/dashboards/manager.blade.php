<div class="row g-4 mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--amber)">
            <div class="metric-card-accent" style="background: var(--amber)"></div>
            <div class="metric-top"><span class="metric-label">Pengeluaran BBM Bulan Ini</span><span class="metric-dot" style="background: var(--amber)"></span></div>
            <div class="metric-value" style="font-size:20px">Rp {{ number_format($data['pengeluaran_bbm_bulan_ini'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--emerald)">
            <div class="metric-card-accent" style="background: var(--emerald)"></div>
            <div class="metric-top"><span class="metric-label">Kendaraan Aktif</span><span class="metric-dot" style="background: var(--emerald)"></span></div>
            <div class="metric-value">{{ $data['kendaraan_aktif'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--crimson)">
            <div class="metric-card-accent" style="background: var(--crimson)"></div>
            <div class="metric-top"><span class="metric-label">Kendaraan Servis</span><span class="metric-dot" style="background: var(--crimson)"></span></div>
            <div class="metric-value">{{ $data['kendaraan_servis'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--sky)">
            <div class="metric-card-accent" style="background: var(--sky)"></div>
            <div class="metric-top"><span class="metric-label">WC Perlu Tindakan</span><span class="metric-dot" style="background: var(--sky)"></span></div>
            <div class="metric-value">{{ $data['wc_status']['perlu_tindakan'] }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                <span class="metric-label">Permintaan ATK Menunggu Persetujuan ({{ $data['atk_menunggu'] }})</span>
                <a href="{{ route('atk.requests.index') }}" class="small">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($data['atk_pending_list'] as $r)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $r->requester->name ?? '—' }} &middot; {{ $r->department }}</span>
                    <a href="{{ route('atk.requests.show', $r) }}" class="small">Lihat</a>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Tidak ada permintaan menunggu.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                <span class="metric-label">Booking Meeting Menunggu Persetujuan ({{ $data['meeting_menunggu'] }})</span>
                <a href="{{ route('meeting.bookings.index') }}" class="small">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($data['meeting_pending_list'] as $b)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $b->nama_kegiatan }} &middot; {{ $b->room->nama_ruangan ?? '—' }}</span>
                    <a href="{{ route('meeting.bookings.show', $b) }}" class="small">Lihat</a>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Tidak ada booking menunggu.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('dashboards.partials.notifications', ['notifications' => $data['notifications']])
