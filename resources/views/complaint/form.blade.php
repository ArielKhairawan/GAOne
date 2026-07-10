@extends('layouts.app')

@section('title', 'Buat Pengaduan')
@section('page-title', 'Buat Pengaduan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Layanan</span>
        <h1 class="section-title">Buat Pengaduan</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('complaint.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ route('complaint.store') }}">
    @csrf
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Judul <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="judul" value="{{ old('judul') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi <span style="color:var(--crimson)">*</span></label>
                    <textarea class="form-control" name="deskripsi" rows="5">{{ old('deskripsi') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Kirim Pengaduan</button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('complaint.index') }}">Batal</a>
    </div>
</form>

@endsection
