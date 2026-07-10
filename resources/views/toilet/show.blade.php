@extends('layouts.app')

@section('title', 'Detail Inspeksi WC')
@section('page-title', 'Detail Inspeksi WC')
@section('page-subtitle', $inspection->lokasi_detail ?: $inspection->lokasi)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Monitoring Kebersihan WC</span>
        <h1 class="section-title">{{ $inspection->lokasi_detail ?: $inspection->lokasi }}</h1>
        <p class="section-subtitle">{{ $inspection->tanggal->format('d M Y') }} &middot; {{ $inspection->jam }} &middot; {{ $inspection->petugas_name }}</p>
    </div>
    <div class="d-flex gap-2">
        @can('toilet.edit')
        <a class="btn btn-outline-secondary" href="{{ route('toilet.edit', $inspection) }}">Edit</a>
        @endcan
        <a class="btn btn-outline-secondary" href="{{ route('toilet.index') }}">Kembali</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
                <div style="font-size:14px; font-weight:600; color:var(--text)">Checklist Kebersihan</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Item</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($inspection->items as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>
                                    <span class="status-badge {{ $item->status === 'baik' ? 'active' : ($item->status === 'rusak' ? 'inactive' : 'pending') }}">
                                        {{ config('monitoring.toilet_checklist_item_statuses')[$item->status] ?? $item->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($inspection->catatan)
        <div class="card">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
                <div style="font-size:14px; font-weight:600; color:var(--text)">Catatan Temuan</div>
            </div>
            <div class="card-body p-4">
                <p class="mb-0">{{ $inspection->catatan }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
                <div style="font-size:14px; font-weight:600; color:var(--text)">Status</div>
            </div>
            <div class="card-body p-4">
                <span class="status-badge {{ $inspection->status === 'bersih' ? 'active' : ($inspection->status === 'kotor' ? 'inactive' : 'pending') }}">
                    {{ config('monitoring.toilet_statuses')[$inspection->status] ?? $inspection->status }}
                </span>
            </div>
        </div>

        @if($inspection->foto)
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
                <div style="font-size:14px; font-weight:600; color:var(--text)">Foto</div>
            </div>
            <div class="card-body p-3">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($inspection->foto) }}" alt="Foto inspeksi" style="width:100%; border-radius:8px">
            </div>
        </div>
        @endif

        @if($inspection->tanda_tangan)
        <div class="card">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
                <div style="font-size:14px; font-weight:600; color:var(--text)">Tanda Tangan Petugas</div>
            </div>
            <div class="card-body p-3">
                <img src="{{ $inspection->tanda_tangan }}" alt="Tanda tangan" style="width:100%; max-width:300px; border:1px solid var(--border); border-radius:8px">
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
