@extends('layouts.app')

@section('title', 'Barang Keluar')
@section('page-title', 'Riwayat Barang Keluar')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Inventaris</span>
        <h1 class="section-title">Barang Keluar</h1>
    </div>
    <a class="btn btn-primary" href="{{ route('atk.stock-out.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Catat Barang Keluar
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Referensi</th><th>Dicatat Oleh</th><th>Catatan</th></tr></thead>
                <tbody>
                    @forelse($movements as $m)
                    <tr>
                        <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                        <td class="fw-medium">{{ $m->item->name ?? '—' }}</td>
                        <td>-{{ $m->quantity }} {{ $m->item->satuan ?? '' }}</td>
                        <td>{{ $m->reference_type ? 'Permintaan #'.$m->reference_id : 'Manual' }}</td>
                        <td>{{ $m->user->name ?? '—' }}</td>
                        <td>{{ $m->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; padding:48px; color:var(--text-3)">Belum ada riwayat barang keluar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">{{ $movements->links() }}</div>

@endsection
