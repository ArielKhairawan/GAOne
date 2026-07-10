@extends('layouts.app')

@section('title', 'Permintaan ATK')
@section('page-title', 'Permintaan ATK')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Inventaris</span>
        <h1 class="section-title">Permintaan ATK</h1>
        <p class="section-subtitle">Total {{ $requests->total() }} permintaan.</p>
    </div>
    @can('atk.create')
    <a class="btn btn-primary" href="{{ route('atk.requests.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Buat Permintaan
    </a>
    @endcan
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Tanggal</th><th>Pemohon</th><th>Departemen</th><th>Jumlah Item</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($requests as $r)
                    <tr>
                        <td>{{ $r->created_at->format('d M Y') }}</td>
                        <td class="fw-medium">{{ $r->requester->name ?? '—' }}</td>
                        <td>{{ $r->department }}</td>
                        <td>{{ $r->items->count() }}</td>
                        <td>
                            <span class="status-badge {{ $r->status === 'approved' ? 'active' : ($r->status === 'rejected' ? 'inactive' : 'pending') }}">
                                {{ $statusLabels[$r->status] ?? $r->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('atk.requests.show', $r) }}">Lihat</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; padding:48px; color:var(--text-3)">Belum ada permintaan ATK.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">{{ $requests->links() }}</div>

@endsection
