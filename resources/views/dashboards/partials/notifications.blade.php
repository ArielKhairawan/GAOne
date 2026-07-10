<div class="card mt-4">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
        <span class="metric-label">Notifikasi Terbaru</span>
        @can('notification.view')
        <a href="{{ route('modules.index', 'notifications') }}" class="small">Lihat Semua</a>
        @endcan
    </div>
    <div class="card-body p-0">
        @forelse($notifications as $n)
        <div style="padding:12px 20px; border-bottom:1px solid var(--border)">
            <div style="font-size:13.5px; font-weight:500">{{ $n->title }}</div>
            <div style="font-size:12.5px; color:var(--text-3)">{{ $n->body }}</div>
            <div style="font-size:11px; color:var(--text-4); margin-top:4px">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @empty
        <div style="padding:32px; text-align:center; color:var(--text-3); font-size:13px">Tidak ada notifikasi terbaru.</div>
        @endforelse
    </div>
</div>
