@extends('layouts.app')

@section('title', 'Buat Permintaan Konsumsi')
@section('page-title', 'Buat Permintaan Konsumsi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">Buat Permintaan Konsumsi</h1>
        <p class="section-subtitle">Gunakan form ini untuk permintaan konsumsi yang tidak terkait booking ruang meeting.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('consumption.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ route('consumption.store') }}">
    @csrf
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal <span style="color:var(--crimson)">*</span></label>
                    <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departemen</label>
                    <input type="text" class="form-control" name="departemen" value="{{ old('departemen', auth()->user()->department) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Peserta <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="1" class="form-control" name="jumlah_peserta" value="{{ old('jumlah_peserta') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Nama Acara <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="nama_acara" value="{{ old('nama_acara') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Jenis Konsumsi <span style="color:var(--crimson)">*</span></label>
                    <div class="row">
                        @foreach(config('monitoring.consumption_types') as $type)
                        <div class="col-md-3 col-6">
                            <div class="form-check mb-1">
                                <input type="checkbox" class="form-check-input" name="jenis_konsumsi[]" id="jk-{{ $loop->index }}" value="{{ $type }}" @checked(in_array($type, old('jenis_konsumsi', [])))>
                                <label class="form-check-label" for="jk-{{ $loop->index }}" style="font-size:13px">{{ $type }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Detail Konsumsi</label>
                    <textarea class="form-control" name="detail_konsumsi" rows="3" placeholder="Cth: Nasi Kotak Ayam Bakar, Air Mineral">{{ old('detail_konsumsi') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Ajukan Permintaan</button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('consumption.index') }}">Batal</a>
    </div>
</form>

@endsection
