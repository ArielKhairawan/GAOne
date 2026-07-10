@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Dashboard & Reporting')
@section('page-subtitle', 'Export laporan operasional dan analitik sistem dalam format PDF atau CSV')

@section('content')

<div class="mb-5">
    <span class="section-eyebrow">Analytics</span>
    <h1 class="section-title">Laporan Operasional</h1>
    <p class="section-subtitle">Unduh data dalam format PDF atau CSV untuk keperluan pelaporan.</p>
</div>

@php
$reports = [
    'travel'  => ['label' => 'Travel',          'icon' => '✈️',  'desc' => 'Biaya perjalanan dinas & akomodasi karyawan.', 'color' => '#3b82f6'],
    'booking' => ['label' => 'Booking',          'icon' => '📅',  'desc' => 'Pemesanan fasilitas, kendaraan, & ruang rapat.', 'color' => '#8b5cf6'],
    'atk'     => ['label' => 'ATK',              'icon' => '📝',  'desc' => 'Pengadaan & pemakaian alat tulis kantor.',        'color' => '#f59e0b'],
    'po'      => ['label' => 'Purchase Order',   'icon' => '🧾',  'desc' => 'Seluruh permintaan pembelian & realisasinya.',   'color' => '#10b981'],
    'csat'    => ['label' => 'CSAT',             'icon' => '⭐',  'desc' => 'Skor kepuasan & umpan balik layanan internal.',  'color' => '#e11d48'],
];
@endphp

<div class="row g-4">
    @foreach($reports as $type => $r)
    <div class="col-lg-4 col-md-6">
        <div class="report-card">
            <div class="report-icon" style="background: {{ $r['color'] }}18">
                <span style="font-size:20px">{{ $r['icon'] }}</span>
            </div>
            <div class="report-title">{{ $r['label'] }}</div>
            <div class="report-description">{{ $r['desc'] }}</div>
            <div class="d-flex gap-2 mt-4">
                <a class="btn btn-outline-danger flex-fill"
                   href="{{ route('reports.export', [$type, 'format' => 'pdf']) }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                    </svg>
                    PDF
                </a>
                <a class="btn btn-outline-success flex-fill"
                   href="{{ route('reports.export', [$type, 'format' => 'csv']) }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    CSV
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@canany(['fuel.export', 'toilet.export', 'atk.export'])
<div class="mb-4 mt-5">
    <span class="section-eyebrow">Monitoring Operasional</span>
    <h2 class="section-title" style="font-size:20px">Laporan Bahan Bakar & Kebersihan WC</h2>
    <p class="section-subtitle">Gunakan filter pada masing-masing halaman monitoring untuk laporan yang lebih spesifik.</p>
</div>

<div class="row g-4">

    @can('fuel.export')
    <div class="col-lg-4 col-md-6">
        <div class="report-card">
            <div class="report-icon" style="background: #f59e0b18">
                <span style="font-size:20px">⛽</span>
            </div>
            <div class="report-title">Bahan Bakar</div>
            <div class="report-description">Riwayat pengisian, total pengeluaran, dan konsumsi BBM seluruh kendaraan.</div>
            <div class="d-flex gap-2 mt-4">
                <a class="btn btn-outline-danger flex-fill" href="{{ route('fuel.export.pdf') }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                    </svg>
                    PDF
                </a>
                <a class="btn btn-outline-success flex-fill" href="{{ route('fuel.export.excel') }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    Excel
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('toilet.export')
    <div class="col-lg-4 col-md-6">
        <div class="report-card">
            <div class="report-icon" style="background: #10b98118">
                <span style="font-size:20px">🧼</span>
            </div>
            <div class="report-title">Kebersihan WC</div>
            <div class="report-description">Riwayat inspeksi, status kebersihan, dan item checklist per lokasi.</div>
            <div class="d-flex gap-2 mt-4">
                <a class="btn btn-outline-danger flex-fill" href="{{ route('toilet.export.pdf') }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                    </svg>
                    PDF
                </a>
                <a class="btn btn-outline-success flex-fill" href="{{ route('toilet.export.excel') }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    Excel
                </a>
            </div>
        </div>
    </div>
    @endcan

    @can('atk.export')
    <div class="col-lg-4 col-md-6">
        <div class="report-card">
            <div class="report-icon" style="background: #3b82f618">
                <span style="font-size:20px">📦</span>
            </div>
            <div class="report-title">Inventaris ATK</div>
            <div class="report-description">Daftar stok, status, dan lokasi penyimpanan seluruh barang ATK.</div>
            <div class="d-flex gap-2 mt-4">
                <a class="btn btn-outline-danger flex-fill" href="{{ route('atk.items.export.pdf') }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                    </svg>
                    PDF
                </a>
                <a class="btn btn-outline-success flex-fill" href="{{ route('atk.items.export.excel') }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    Excel
                </a>
            </div>
        </div>
    </div>
    @endcan

</div>
@endcanany

@endsection
