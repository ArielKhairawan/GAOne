@extends('layouts.app')

@section('title', 'Detail Permintaan Konsumsi')
@section('page-title', 'Detail Permintaan Konsumsi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">{{ $consumptionRequest->nama_acara }}</h1>
        <p class="section-subtitle">{{ $consumptionRequest->tanggal->format('d M Y') }} &middot; {{ $consumptionRequest->requester->name ?? '—' }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('consumption.index') }}">Kembali</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Detail Permintaan</div></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6"><div class="small text-muted">Departemen</div><div>{{ $consumptionRequest->departemen ?? '—' }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">Jumlah Peserta</div><div>{{ $consumptionRequest->jumlah_peserta }} orang</div></div>
                    <div class="col-12"><div class="small text-muted">Jenis Konsumsi</div><div>{{ implode(', ', $consumptionRequest->jenis_konsumsi ?? []) }}</div></div>
                    @if($consumptionRequest->detail_konsumsi)
                    <div class="col-12"><div class="small text-muted">Detail Konsumsi</div><div>{{ $consumptionRequest->detail_konsumsi }}</div></div>
                    @endif
                    @if($consumptionRequest->meetingBooking)
                    <div class="col-12"><div class="small text-muted">Terkait Booking</div><div>{{ $consumptionRequest->meetingBooking->nama_kegiatan }}</div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Status</div></div>
            <div class="card-body p-4">
                <span class="status-badge {{ in_array($consumptionRequest->status, ['approved','selesai']) ? 'active' : ($consumptionRequest->status === 'rejected' ? 'inactive' : 'pending') }}">
                    {{ $statusLabels[$consumptionRequest->status] ?? $consumptionRequest->status }}
                </span>
            </div>
        </div>

        @can('consumption.approve')
        @if(in_array($consumptionRequest->status, ['submitted', 'revision']))
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Tindakan Approval</div></div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('consumption.act', $consumptionRequest) }}" class="mb-2">
                    @csrf<input type="hidden" name="action" value="approve">
                    <button class="btn btn-primary" style="width:100%; justify-content:center">Setujui</button>
                </form>
                <form method="post" action="{{ route('consumption.act', $consumptionRequest) }}">
                    @csrf<input type="hidden" name="action" value="reject">
                    <button class="btn btn-outline-danger" style="width:100%; justify-content:center">Tolak</button>
                </form>
            </div>
        </div>
        @endif
        @endcan

        @can('consumption.edit')
        @if($consumptionRequest->status === 'approved')
        <div class="card">
            <div class="card-body p-4">
                <form method="post" action="{{ route('consumption.processing', $consumptionRequest) }}" class="mb-2">
                    @csrf
                    <button class="btn btn-outline-primary" style="width:100%; justify-content:center">Tandai Diproses</button>
                </form>
                <form method="post" action="{{ route('consumption.complete', $consumptionRequest) }}">
                    @csrf
                    <button class="btn btn-outline-primary" style="width:100%; justify-content:center">Tandai Selesai</button>
                </form>
            </div>
        </div>
        @elseif($consumptionRequest->status === 'diproses')
        <div class="card">
            <div class="card-body p-4">
                <form method="post" action="{{ route('consumption.complete', $consumptionRequest) }}">
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
