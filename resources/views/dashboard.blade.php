@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas sesuai role Anda')

@section('content')

    <!-- KUNCI BERSIH: Cuma panggil admin.blade.php di sini. JANGAN ADA KOTAK DIV LAIN LAGI! -->
    @include('dashboards.' . $roleView, ['data' => $data, 'user' => $user])

@endsection

@if(isset($monitoringStats))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Opsi standar untuk grafik batang & garis (BBM & Toilet)
    const lineBarOptions = {
        responsive: true,
        maintainAspectRatio: false
    };

    @can('fuel.view')
    new Chart(document.getElementById('chartFuelMonthly'), {
        type: 'bar',
        data: {
            labels: @json($monitoringCharts['fuel_monthly_spending']->pluck('label')),
            datasets: [{ label: 'Pengeluaran BBM (Rp)', data: @json($monitoringCharts['fuel_monthly_spending']->pluck('total')), backgroundColor: '#f59e0b', borderRadius: 6 }],
        },
        options: { ...lineBarOptions, plugins: { legend: { display: false } } },
    });
    @endcan

    @can('toilet.view')
    new Chart(document.getElementById('chartToiletActivity'), {
        type: 'line',
        data: {
            labels: @json($monitoringCharts['toilet_activity']->pluck('label')),
            datasets: [
                { label: 'Total Inspeksi', data: @json($monitoringCharts['toilet_activity']->pluck('total')), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.15)', tension: 0.35, fill: true },
                { label: 'Status Kotor', data: @json($monitoringCharts['toilet_activity']->pluck('kotor')), borderColor: '#e11d48', backgroundColor: 'rgba(225,29,72,.1)', tension: 0.35, fill: true },
            ],
        },
        options: lineBarOptions,
    });
    @endcan

    @can('vehicle.view')
    new Chart(document.getElementById('chartVehicleStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Servis', 'Tidak Aktif'],
            datasets: [{
                data: [
                    {{ $monitoringStats['vehicle']['unit_aktif'] }},
                    {{ $monitoringStats['vehicle']['unit_servis'] }},
                    {{ $monitoringStats['vehicle']['unit_tidak_aktif'] }},
                ],
                backgroundColor: ['#0ea5e9', '#f59e0b', '#e11d48'],
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            // KUNCI FIX: Matikan paksa garis ukur (scales) yang nyasar ke grafik donat!
            scales: {
                x: { display: false },
                y: { display: false }
            },
            plugins: {
                legend: { position: 'top' }
            }
        },
    });
    @endcan
});
</script>
@endpush
@endif
