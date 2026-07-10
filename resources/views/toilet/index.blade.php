@extends('layouts.app')

@section('title', 'Monitoring Kebersihan WC')
@section('page-title', 'Monitoring Kebersihan WC')
@section('page-subtitle', 'Catatan inspeksi kebersihan toilet')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Monitoring Operasional</span>
        <h1 class="section-title">Monitoring Kebersihan WC</h1>
        <p class="section-subtitle">Total {{ $inspections->total() }} data inspeksi.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('toilet.export')
        <a class="btn btn-outline-danger" href="{{ route('toilet.export.pdf', $filters) }}">PDF</a>
        <a class="btn btn-outline-success" href="{{ route('toilet.export.excel', $filters) }}">Excel</a>
        @endcan
        @can('toilet.create')
        <a class="btn btn-primary" href="{{ route('toilet.create') }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Tambah Inspeksi
        </a>
        @endcan
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-2">
    <div class="col-xl-4">
        <div class="metric-card project-card" style="border-left-color: var(--sky)">
            <div class="metric-card-accent" style="background: var(--sky)"></div>
            <div class="metric-top"><span class="metric-label">Total Inspeksi Hari Ini</span><span class="metric-dot" style="background: var(--sky)"></span></div>
            <div class="metric-value">{{ $stats['total_hari_ini'] }}</div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="metric-card project-card" style="border-left-color: var(--emerald)">
            <div class="metric-card-accent" style="background: var(--emerald)"></div>
            <div class="metric-top"><span class="metric-label">WC Bersih</span><span class="metric-dot" style="background: var(--emerald)"></span></div>
            <div class="metric-value">{{ $stats['bersih'] }}</div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="metric-card project-card" style="border-left-color: var(--crimson)">
            <div class="metric-card-accent" style="background: var(--crimson)"></div>
            <div class="metric-top"><span class="metric-label">WC Perlu Tindakan</span><span class="metric-dot" style="background: var(--crimson)"></span></div>
            <div class="metric-value">{{ $stats['perlu_tindakan'] }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<form method="get" class="card mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-2">
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Lokasi</label>
                <select name="lokasi" class="form-select">
                    <option value="">— Semua Lokasi —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" @selected(($filters['lokasi'] ?? '') === $loc)>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Petugas</label>
                <input type="text" name="petugas" class="form-control" value="{{ $filters['petugas'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">— Semua —</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-primary" style="width:100%; justify-content:center; margin-top:22px">Filter</button>
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
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Lokasi</th>
                        <th>Petugas</th>
                        <th>Status</th>
                        <th>Item Bermasalah</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $inspection)
                    <tr>
                        <td>{{ $inspection->tanggal->format('d M Y') }}</td>
                        <td>{{ $inspection->jam }}</td>
                        <td class="fw-medium">{{ $inspection->lokasi_detail ?: $inspection->lokasi }}</td>
                        <td>{{ $inspection->petugas_name ?? '—' }}</td>
                        <td>
                            <span class="status-badge {{ $inspection->status === 'bersih' ? 'active' : ($inspection->status === 'kotor' ? 'inactive' : 'pending') }}">
                                {{ $statuses[$inspection->status] ?? $inspection->status }}
                            </span>
                        </td>
                        <td>
                            @php $bermasalah = $inspection->items->whereIn('status', ['kurang', 'rusak'])->count(); @endphp
                            {{ $bermasalah > 0 ? $bermasalah . ' item' : '—' }}
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('toilet.show', $inspection) }}">Lihat</a>
                                @can('toilet.edit')
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('toilet.edit', $inspection) }}">Edit</a>
                                @endcan
                                @can('toilet.delete')
                                <form class="d-inline" method="post" action="{{ route('toilet.destroy', $inspection) }}" onsubmit="return confirm('Hapus data inspeksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:48px; color:var(--text-3); font-size:14px">
                            Belum ada data inspeksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $inspections->links() }}
</div>

@endsection
