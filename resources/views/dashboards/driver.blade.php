<div class="card mb-4">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Kendaraan Saya</span></div>
    <div class="card-body p-0">
        @forelse($data['kendaraan_saya'] as $v)
        <div style="padding:14px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
            <div>
                <div style="font-size:14px; font-weight:600">{{ $v->plat_nomor }}</div>
                <div style="font-size:12.5px; color:var(--text-3)">{{ $v->jenis_kendaraan }} &middot; {{ $v->merk }}</div>
            </div>
            <span class="status-badge {{ $v->status === 'aktif' ? 'active' : ($v->status === 'tidak_aktif' ? 'inactive' : 'pending') }}">
                {{ config('monitoring.vehicle_statuses')[$v->status] }}
            </span>
        </div>
        @empty
        <div style="padding:32px; text-align:center; color:var(--text-3); font-size:13px">Belum ada kendaraan yang ditugaskan ke akun Anda. Hubungi GA Staff untuk menghubungkan akun Anda ke kendaraan terkait.</div>
        @endforelse
    </div>
</div>

<div class="card">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
        <span class="metric-label">Riwayat Pengisian BBM</span>
        @can('fuel.create')
        <a href="{{ route('fuel.create') }}" class="small">+ Catat Pengisian</a>
        @endcan
    </div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead><tr><th>Tanggal</th><th>Kendaraan</th><th>Liter</th><th>Total</th><th>Jarak</th></tr></thead>
            <tbody>
                @forelse($data['riwayat_bbm'] as $f)
                <tr>
                    <td>{{ $f->tanggal_pengisian->format('d M Y') }}</td>
                    <td>{{ $f->vehicle->plat_nomor ?? '—' }}</td>
                    <td>{{ number_format($f->jumlah_liter, 1) }} L</td>
                    <td>Rp {{ number_format($f->total_harga, 0, ',', '.') }}</td>
                    <td>{{ number_format($f->jarak_tempuh) }} km</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:32px; color:var(--text-3)">Belum ada riwayat pengisian BBM.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<p class="small text-muted mt-3">
    Catatan: "Riwayat Perjalanan" dan "Jadwal Kendaraan" belum memiliki modul pencatatan tersendiri di sistem ini —
    riwayat pengisian BBM di atas sementara berfungsi sebagai catatan aktivitas kendaraan. Lihat REFACTOR_NOTES.md.
</p>

@include('dashboards.partials.notifications', ['notifications' => $data['notifications']])
