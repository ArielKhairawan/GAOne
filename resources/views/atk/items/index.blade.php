@extends('layouts.app')

@section('title', 'Data ATK')
@section('page-title', 'Inventaris ATK')
@section('page-subtitle', 'Master data alat tulis kantor')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Inventaris</span>
        <h1 class="section-title">Data ATK</h1>
        <p class="section-subtitle">Total {{ $items->total() }} jenis barang. {{ $lowStockCount }} item perlu perhatian (stok menipis/habis).</p>
    </div>
    @can('atk.create')
    <a class="btn btn-primary" href="{{ route('atk.items.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Tambah Barang
    </a>
    @endcan
</div>

<div class="d-flex justify-content-end gap-2 mb-3">
    @can('atk.export')
    <a class="btn btn-outline-danger" href="{{ route('atk.items.export.pdf') }}">Export PDF</a>
    <a class="btn btn-outline-success" href="{{ route('atk.items.export.excel') }}">Export Excel</a>
    @endcan
</div>

<form method="get" class="card mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode barang..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <select name="atk_category_id" class="form-select">
                    <option value="">— Semua Kategori —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(($filters['atk_category_id'] ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary" style="width:100%; justify-content:center">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th>
                        <th>Stok</th><th>Min. Stok</th><th>Lokasi</th><th>Status</th><th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td class="fw-medium">{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? '—' }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->stock }}</td>
                        <td>{{ $item->minimum_stock }}</td>
                        <td>{{ $item->lokasi_penyimpanan ?? '—' }}</td>
                        <td>
                            <span class="status-badge {{ $item->status === 'tersedia' ? 'active' : ($item->status === 'habis' ? 'inactive' : 'pending') }}">
                                {{ config('monitoring.atk_item_statuses')[$item->status] }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                @can('atk.edit')
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('atk.items.edit', $item) }}">Edit</a>
                                @endcan
                                @can('atk.delete')
                                <form method="post" action="{{ route('atk.items.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center; padding:48px; color:var(--text-3)">Belum ada data barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">{{ $items->links() }}</div>

@endsection
