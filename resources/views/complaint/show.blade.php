@extends('layouts.app')

@section('title', 'Detail Pengaduan')
@section('page-title', 'Detail Pengaduan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Layanan</span>
        <h1 class="section-title">{{ $complaintItem->judul }}</h1>
        <p class="section-subtitle">{{ $complaintItem->user->name ?? '—' }} &middot; {{ $complaintItem->created_at->format('d M Y') }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('complaint.index') }}">Kembali</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Deskripsi</div></div>
            <div class="card-body p-4"><p class="mb-0">{{ $complaintItem->deskripsi }}</p></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Status</div></div>
            <div class="card-body p-4">
                <span class="status-badge {{ $complaintItem->status === 'selesai' ? 'active' : 'pending' }}">
                    {{ $statusLabels[$complaintItem->status] ?? $complaintItem->status }}
                </span>
                @if($complaintItem->resolver)
                <p class="small text-muted mt-2 mb-0">Diselesaikan oleh {{ $complaintItem->resolver->name }} pada {{ $complaintItem->resolved_at?->format('d M Y H:i') }}</p>
                @endif
            </div>
        </div>

        @can('complaint.edit')
        @if($complaintItem->status !== 'selesai')
        <div class="card">
            <div class="card-body p-4">
                @if($complaintItem->status === 'menunggu')
                <form method="post" action="{{ route('complaint.processing', $complaintItem) }}" class="mb-2">
                    @csrf
                    <button class="btn btn-outline-primary" style="width:100%; justify-content:center">Tandai Diproses</button>
                </form>
                @endif
                <form method="post" action="{{ route('complaint.resolve', $complaintItem) }}">
                    @csrf
                    <button class="btn btn-primary" style="width:100%; justify-content:center">Tandai Selesai</button>
                </form>
            </div>
        </div>
        @endif
        @endcan
    </div>
</div>

@endsection
