@extends('layouts.app')

@section('title', ($room->exists ? 'Edit' : 'Tambah') . ' Ruangan')
@section('page-title', ($room->exists ? 'Edit' : 'Tambah') . ' Ruangan Meeting')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">{{ $room->exists ? 'Edit Ruangan' : 'Tambah Ruangan' }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('meeting.rooms.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ $room->exists ? route('meeting.rooms.update', $room) : route('meeting.rooms.store') }}" enctype="multipart/form-data">
    @csrf
    @if($room->exists) @method('PUT') @endif

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Ruangan <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="kode_ruangan" value="{{ old('kode_ruangan', $room->kode_ruangan) }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Ruangan <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="nama_ruangan" value="{{ old('nama_ruangan', $room->nama_ruangan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi</label>
                    <input type="text" class="form-control" name="lokasi" value="{{ old('lokasi', $room->lokasi) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kapasitas <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="1" class="form-control" name="kapasitas" value="{{ old('kapasitas', $room->kapasitas) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="status">
                        @foreach(config('monitoring.meeting_room_statuses') as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $room->status ?? 'tersedia') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Foto Ruangan</label>
                    <input type="file" class="form-control" name="foto" accept="image/jpeg,image/png,image/webp">
                    @if($room->foto)
                        <small class="text-muted">Foto saat ini tersimpan. Unggah file baru untuk mengganti.</small>
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="2">{{ old('deskripsi', $room->deskripsi) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Fasilitas Ruangan</label>
                    <div class="row">
                        @foreach(config('monitoring.meeting_room_facilities') as $facility)
                        <div class="col-md-3 col-6">
                            <div class="form-check mb-1">
                                <input type="checkbox" class="form-check-input" name="fasilitas[]" id="fac-{{ $loop->index }}"
                                       value="{{ $facility }}" @checked(in_array($facility, old('fasilitas', $room->fasilitas ?? [])))>
                                <label class="form-check-label" for="fac-{{ $loop->index }}" style="font-size:13px">{{ $facility }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Simpan</button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('meeting.rooms.index') }}">Batal</a>
    </div>
</form>

@endsection
