@extends('layouts.app')

@section('title', 'Monitoring Bahan Bakar')
@section('page-title', 'Monitoring Bahan Bakar')
@section('page-subtitle', 'Catatan pengisian BBM seluruh kendaraan operasional')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Monitoring Operasional</span>
        <h1 class="section-title">Monitoring Bahan Bakar</h1>
        <p class="section-subtitle">Total {{ $logs->total() }} data pengisian BBM.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('fuel.export')
        <a class="btn btn-outline-danger" href="{{ route('fuel.export.pdf', $filters) }}">PDF</a>
        <a class="btn btn-outline-success" href="{{ route('fuel.export.excel', $filters) }}">Excel</a>
        @endcan
        @can('fuel.create')
        <a class="btn btn-primary" href="{{ route('fuel.create') }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Tambah
        </a>
        @endcan
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--amber)">
            <div class="metric-card-accent" style="background: var(--amber)"></div>
            <div class="metric-top"><span class="metric-label">Pengeluaran Bulan Ini</span><span class="metric-dot" style="background: var(--amber)"></span></div>
            <div class="metric-value" style="font-size:20px">Rp {{ number_format($stats['total_pengeluaran_bulan_ini'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--sky)">
            <div class="metric-card-accent" style="background: var(--sky)"></div>
            <div class="metric-top"><span class="metric-label">Total Liter Bulan Ini</span><span class="metric-dot" style="background: var(--sky)"></span></div>
            <div class="metric-value">{{ number_format($stats['total_liter_bulan_ini'], 1) }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--emerald)">
            <div class="metric-card-accent" style="background: var(--emerald)"></div>
            <div class="metric-top"><span class="metric-label">Total Pengisian</span><span class="metric-dot" style="background: var(--emerald)"></span></div>
            <div class="metric-value">{{ $stats['total_pengisian_bulan_ini'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card project-card" style="border-left-color: var(--crimson)">
            <div class="metric-card-accent" style="background: var(--crimson)"></div>
            <div class="metric-top"><span class="metric-label">Rata-rata Konsumsi</span><span class="metric-dot" style="background: var(--crimson)"></span></div>
            <div class="metric-value">{{ $stats['rata_rata_konsumsi'] }} km/l</div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row g-4 mb-2">
    <div class="col-lg-6">
        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Pengeluaran BBM per Bulan</span></div>
            <div class="card-body p-3"><canvas id="chartFuelMonthly" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border)"><span class="metric-label">Konsumsi BBM per Kendaraan (km/liter)</span></div>
            <div class="card-body p-3"><canvas id="chartConsumption" height="220"></canvas></div>
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
            <div class="col-md-2">
                <label class="form-label">Driver</label>
                <input type="text" name="driver" class="form-control" value="{{ $filters['driver'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Plat Kendaraan</label>
                <input type="text" name="plat_nomor" class="form-control" value="{{ $filters['plat_nomor'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Jenis BBM</label>
                <select name="jenis_bahan_bakar" class="form-select">
                    <option value="">— Semua —</option>
                    @foreach($fuelTypes as $type)
                        <option value="{{ $type }}" @selected(($filters['jenis_bahan_bakar'] ?? '') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
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
                        <th>Plat Kendaraan</th>
                        <th>Driver</th>
                        <th>Jenis BBM</th>
                        <th>Liter</th>
                        <th>Total Harga</th>
                        <th>Jarak (km)</th>
                        <th>Konsumsi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->tanggal_pengisian->format('d M Y') }}</td>
                        <td class="fw-medium">{{ $log->vehicle->plat_nomor ?? '—' }}</td>
                        <td>{{ $log->driver_name ?? '—' }}</td>
                        <td>{{ $log->jenis_bahan_bakar }}</td>
                        <td>{{ number_format($log->jumlah_liter, 1) }}</td>
                        <td>Rp {{ number_format($log->total_harga, 0, ',', '.') }}</td>
                        <td>{{ number_format($log->jarak_tempuh) }}</td>
                        <td>{{ $log->konsumsi_bbm ?? '—' }} km/l</td>
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                @can('fuel.edit')
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('fuel.edit', $log) }}">Edit</a>
                                @endcan
                                @can('fuel.delete')
                                <form class="d-inline" method="post" action="{{ route('fuel.destroy', $log) }}" onsubmit="return confirm('Hapus data ini?')">
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
                        <td colspan="9" style="text-align:center; padding:48px; color:var(--text-3); font-size:14px">
                            Belum ada data pengisian BBM.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('chartFuelMonthly'), {
        type: 'bar',
        data: {
            labels: @json($monthlyChart->pluck('label')),
            datasets: [{
                label: 'Pengeluaran (Rp)',
                data: @json($monthlyChart->pluck('total')),
                backgroundColor: '#f59e0b',
                borderRadius: 6,
            }],
        },
        options: { responsive: true, plugins: { legend: { display: false } } },
    });

    new Chart(document.getElementById('chartConsumption'), {
        type: 'bar',
        data: {
            labels: @json($consumptionChart->pluck('plat_nomor')),
            datasets: [{
                label: 'Rata-rata km/liter',
                data: @json($consumptionChart->pluck('avg_konsumsi')),
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }],
        },
        options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } } },
    });
});
</script>
@endpush
