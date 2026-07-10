@extends('layouts.app')

@section('title', 'Detail Booking')
@section('page-title', 'Detail Booking Ruang Meeting')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">{{ $booking->nama_kegiatan }}</h1>
        <p class="section-subtitle">{{ $booking->room->nama_ruangan ?? '—' }} &middot; {{ $booking->tanggal->format('d M Y') }} &middot; {{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('meeting.bookings.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">{{ $errors->first() }}</div>
@endif

<div class="row g-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Detail Permohonan</div></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6"><div class="small text-muted">Pemohon</div><div>{{ $booking->requester->name ?? '—' }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Departemen</div><div>{{ $booking->departemen ?? '—' }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Jumlah Peserta</div><div>{{ $booking->jumlah_peserta }} orang</div></div>
                    <div class="col-md-6"><div class="small text-muted">Butuh Konsumsi</div><div>{{ $booking->butuh_konsumsi ? 'Ya' : 'Tidak' }}</div></div>
                    @if($booking->catatan)
                    <div class="col-12"><div class="small text-muted">Catatan</div><div>{{ $booking->catatan }}</div></div>
                    @endif
                </div>
            </div>
        </div>

        @if($booking->consumptionRequest->isNotEmpty())
        <div class="card">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Permintaan Konsumsi Terkait</div></div>
            <div class="card-body p-4">
                @foreach($booking->consumptionRequest as $cr)
                <div class="mb-2">
                    <strong>{{ implode(', ', $cr->jenis_konsumsi) }}</strong>
                    <p class="small text-muted mb-0">{{ $cr->detail_konsumsi }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Status</div></div>
            <div class="card-body p-4">
                <span class="status-badge {{ in_array($booking->status, ['approved','selesai']) ? 'active' : ($booking->status === 'rejected' ? 'inactive' : 'pending') }}">
                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                </span>
            </div>
        </div>

        @can('meeting.approve')
        @if(in_array($booking->status, ['submitted', 'revision']))
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Tindakan Approval</div></div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('meeting.bookings.act', $booking) }}" class="mb-2">
                    @csrf<input type="hidden" name="action" value="approve">
                    <button class="btn btn-primary" style="width:100%; justify-content:center" onclick="return confirm('Setujui booking ini?')">Setujui</button>
                </form>
                <form method="post" action="{{ route('meeting.bookings.act', $booking) }}">
                    @csrf<input type="hidden" name="action" value="reject">
                    <button class="btn btn-outline-danger" style="width:100%; justify-content:center" onclick="return confirm('Tolak booking ini?')">Tolak</button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        @can('meeting.edit')
        @if($booking->status === 'approved' && $booking->tanggal->isPast())
        <div class="card">
            <div class="card-body p-4">
                <form method="post" action="{{ route('meeting.bookings.complete', $booking) }}">
                    @csrf
                    <button class="btn btn-outline-primary" style="width:100%; justify-content:center">Tandai Selesai</button>
                </form>
            </div>
        </div>
        @endif
        @endcan
    </div>
</div>

@endsection
