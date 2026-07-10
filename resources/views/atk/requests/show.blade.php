@extends('layouts.app')

@section('title', 'Detail Permintaan ATK')
@section('page-title', 'Detail Permintaan ATK #' . $atkRequest->id)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Inventaris</span>
        <h1 class="section-title">Permintaan #{{ $atkRequest->id }}</h1>
        <p class="section-subtitle">{{ $atkRequest->requester->name ?? '—' }} &middot; {{ $atkRequest->department }} &middot; {{ $atkRequest->created_at->format('d M Y') }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('atk.requests.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">{{ $errors->first() }}</div>
@endif

<div class="row g-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600; color:var(--text)">Daftar Barang Diminta</div></div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Barang</th><th>Jumlah</th><th>Stok Saat Ini</th></tr></thead>
                    <tbody>
                        @foreach($atkRequest->items as $line)
                        <tr>
                            <td>{{ $line->item->name ?? '—' }}</td>
                            <td>{{ $line->quantity }} {{ $line->item->satuan ?? '' }}</td>
                            <td>{{ $line->item->stock ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($atkRequest->notes)
        <div class="card">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600; color:var(--text)">Catatan</div></div>
            <div class="card-body p-4"><p class="mb-0">{{ $atkRequest->notes }}</p></div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600; color:var(--text)">Status</div></div>
            <div class="card-body p-4">
                <span class="status-badge {{ $atkRequest->status === 'approved' ? 'active' : ($atkRequest->status === 'rejected' ? 'inactive' : 'pending') }}">
                    {{ $statusLabels[$atkRequest->status] ?? $atkRequest->status }}
                </span>
            </div>
        </div>

        @can('atk.approve')
        @if(in_array($atkRequest->status, ['submitted', 'revision']))
        <div class="card">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600; color:var(--text)">Tindakan Approval</div></div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('atk.requests.act', $atkRequest) }}" class="mb-2">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <button class="btn btn-primary" style="width:100%; justify-content:center" onclick="return confirm('Setujui permintaan ini?')">Setujui</button>
                </form>
                <form method="post" action="{{ route('atk.requests.act', $atkRequest) }}">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <button class="btn btn-outline-danger" style="width:100%; justify-content:center" onclick="return confirm('Tolak permintaan ini?')">Tolak</button>
                </form>
            </div>
        </div>
        @endif
        @endcan
    </div>
</div>

@endsection
