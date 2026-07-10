@extends('layouts.app')

@section('title', 'Permintaan Konsumsi')
@section('page-title', 'Permintaan Konsumsi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">Permintaan Konsumsi</h1>
        <p class="section-subtitle">Total {{ $requests->total() }} permintaan.</p>
    </div>
    @can('consumption.create')
    <a class="btn btn-primary" href="{{ route('consumption.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Buat Permintaan
    </a>
    @endcan
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Tanggal</th><th>Acara</th><th>Pemohon</th><th>Jenis Konsumsi</th><th>Sumber</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($requests as $r)
                    <tr>
                        <td>{{ $r->tanggal->format('d M Y') }}</td>
                        <td class="fw-medium">{{ $r->nama_acara }}</td>
                        <td>{{ $r->requester->name ?? '—' }}</td>
                        <td style="font-size:12px">{{ implode(', ', $r->jenis_konsumsi ?? []) }}</td>
                        <td>{{ $r->meeting_booking_id ? 'Booking Meeting' : 'Mandiri' }}</td>
                        <td>
                            <span class="status-badge {{ in_array($r->status, ['approved','selesai']) ? 'active' : ($r->status === 'rejected' ? 'inactive' : 'pending') }}">
                                {{ $statusLabels[$r->status] ?? $r->status }}
                            </span>
                        </td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('consumption.show', $r) }}">Lihat</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; padding:48px; color:var(--text-3)">Belum ada permintaan konsumsi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">{{ $requests->links() }}</div>

@endsection
