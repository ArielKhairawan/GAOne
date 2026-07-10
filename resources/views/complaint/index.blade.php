@extends('layouts.app')

@section('title', 'Pengaduan')
@section('page-title', 'Pengaduan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Layanan</span>
        <h1 class="section-title">Pengaduan</h1>
        <p class="section-subtitle">Total {{ $complaints->total() }} pengaduan.</p>
    </div>
    @can('complaint.create')
    <a class="btn btn-primary" href="{{ route('complaint.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Buat Pengaduan
    </a>
    @endcan
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Tanggal</th><th>Judul</th><th>Pelapor</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($complaints as $c)
                    <tr>
                        <td>{{ $c->created_at->format('d M Y') }}</td>
                        <td class="fw-medium">{{ $c->judul }}</td>
                        <td>{{ $c->user->name ?? '—' }}</td>
                        <td>
                            <span class="status-badge {{ $c->status === 'selesai' ? 'active' : 'pending' }}">
                                {{ $statusLabels[$c->status] ?? $c->status }}
                            </span>
                        </td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('complaint.show', $c) }}">Lihat</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center; padding:48px; color:var(--text-3)">Belum ada pengaduan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">{{ $complaints->links() }}</div>

@endsection
