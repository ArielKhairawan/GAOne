@extends('layouts.app')

@section('title', 'Monitoring Kendaraan')
@section('page-title', 'Monitoring Kendaraan')
@section('page-subtitle', 'Kelola data master kendaraan operasional')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Monitoring Operasional</span>
        <h1 class="section-title">Monitoring Kendaraan</h1>
        <p class="section-subtitle">Total {{ $vehicles->total() }} kendaraan terdaftar.</p>
    </div>
    @can('vehicle.create')
    <a class="btn btn-primary" href="{{ route('vehicle.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Tambah Kendaraan
    </a>
    @endcan
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--sky)">
            <div class="metric-card-accent" style="background: var(--sky)"></div>
            <div class="metric-top"><span class="metric-label">Total Unit</span><span class="metric-dot" style="background: var(--sky)"></span></div>
            <div class="metric-value">{{ $stats['total_unit'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--emerald)">
            <div class="metric-card-accent" style="background: var(--emerald)"></div>
            <div class="metric-top"><span class="metric-label">Unit Aktif</span><span class="metric-dot" style="background: var(--emerald)"></span></div>
            <div class="metric-value">{{ $stats['unit_aktif'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--amber)">
            <div class="metric-card-accent" style="background: var(--amber)"></div>
            <div class="metric-top"><span class="metric-label">Unit Servis</span><span class="metric-dot" style="background: var(--amber)"></span></div>
            <div class="metric-value">{{ $stats['unit_servis'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--crimson)">
            <div class="metric-card-accent" style="background: var(--crimson)"></div>
            <div class="metric-top"><span class="metric-label">Tidak Aktif</span><span class="metric-dot" style="background: var(--crimson)"></span></div>
            <div class="metric-value">{{ $stats['unit_tidak_aktif'] }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<form method="get" class="card mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari plat nomor, merk, atau driver..."
                       value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">— Semua Status —</option>
                    @foreach(config('monitoring.vehicle_statuses') as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
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
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Merk</th>
                        <th>Tahun</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr>
                        <td class="fw-medium">{{ $vehicle->plat_nomor }}</td>
                        <td>{{ $vehicle->jenis_kendaraan }}</td>
                        <td>{{ $vehicle->merk ?? '—' }}</td>
                        <td>{{ $vehicle->tahun ?? '—' }}</td>
                        <td>{{ $vehicle->driver_name ?? '—' }}</td>
                        <td>
                            <span class="status-badge {{ $vehicle->status === 'aktif' ? 'active' : ($vehicle->status === 'tidak_aktif' ? 'inactive' : 'pending') }}">
                                {{ config('monitoring.vehicle_statuses')[$vehicle->status] ?? $vehicle->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                @can('vehicle.edit')
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('vehicle.edit', $vehicle) }}">Edit</a>
                                @endcan
                                @can('vehicle.delete')
                                <form class="d-inline" method="post" action="{{ route('vehicle.destroy', $vehicle) }}" onsubmit="return confirm('Hapus kendaraan ini?')">
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
                            Belum ada data kendaraan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $vehicles->links() }}
</div>

@endsection
