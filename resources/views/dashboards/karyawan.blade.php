<div class="row g-4">
    <div class="col-md-6">
        <div class="card mb-4">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                <span class="metric-label">Pengaduan Saya</span>
                @can('complaint.create')<a href="{{ route('complaint.create') }}" class="small">+ Buat Baru</a>@endcan
            </div>
            <div class="card-body p-0">
                @forelse($data['pengaduan_saya'] as $c)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $c->judul }}</span>
                    <span class="status-badge {{ $c->status === 'selesai' ? 'active' : 'pending' }}">{{ config('monitoring.complaint_statuses')[$c->status] }}</span>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Belum ada pengaduan.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                <span class="metric-label">Permintaan ATK Saya</span>
                @can('atk.create')<a href="{{ route('atk.requests.create') }}" class="small">+ Buat Baru</a>@endcan
            </div>
            <div class="card-body p-0">
                @forelse($data['atk_saya'] as $r)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $r->department }} &middot; {{ $r->created_at->format('d M') }}</span>
                    <a href="{{ route('atk.requests.show', $r) }}" class="small">Lihat</a>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Belum ada permintaan ATK.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                <span class="metric-label">Booking Saya</span>
                @can('meeting.create')<a href="{{ route('meeting.bookings.create') }}" class="small">+ Buat Baru</a>@endcan
            </div>
            <div class="card-body p-0">
                @forelse($data['booking_saya'] as $b)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $b->nama_kegiatan }} &middot; {{ $b->room->nama_ruangan ?? '—' }}</span>
                    <a href="{{ route('meeting.bookings.show', $b) }}" class="small">Lihat</a>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Belum ada booking.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                <span class="metric-label">Permintaan Konsumsi Saya</span>
                @can('consumption.create')<a href="{{ route('consumption.create') }}" class="small">+ Buat Baru</a>@endcan
            </div>
            <div class="card-body p-0">
                @forelse($data['konsumsi_saya'] as $k)
                <div style="padding:12px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between">
                    <span style="font-size:13.5px">{{ $k->nama_acara }}</span>
                    <a href="{{ route('consumption.show', $k) }}" class="small">Lihat</a>
                </div>
                @empty
                <div style="padding:24px; text-align:center; color:var(--text-3); font-size:13px">Belum ada permintaan konsumsi.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('dashboards.partials.notifications', ['notifications' => $data['notifications']])
