@extends('layouts.app')

@section('title', ($vehicle->exists ? 'Edit' : 'Tambah') . ' Kendaraan')
@section('page-title', ($vehicle->exists ? 'Edit' : 'Tambah') . ' Kendaraan')
@section('page-subtitle', 'Lengkapi data master kendaraan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Monitoring Kendaraan</span>
        <h1 class="section-title">{{ $vehicle->exists ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}</h1>
        <p class="section-subtitle">{{ $vehicle->exists ? 'Perbarui data kendaraan ' . $vehicle->plat_nomor : 'Lengkapi semua kolom yang diperlukan.' }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('vehicle.index') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="post" action="{{ $vehicle->exists ? route('vehicle.update', $vehicle) : route('vehicle.store') }}">
    @csrf
    @if($vehicle->exists) @method('PUT') @endif

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
            <div style="font-size:14px; font-weight:600; color:var(--text)">Informasi Kendaraan</div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Plat Nomor <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="plat_nomor" value="{{ old('plat_nomor', $vehicle->plat_nomor) }}" placeholder="B 1234 ABC">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jenis Kendaraan <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="jenis_kendaraan" list="jenisKendaraanOptions" value="{{ old('jenis_kendaraan', $vehicle->jenis_kendaraan) }}" placeholder="Mobil Operasional">
                    <datalist id="jenisKendaraanOptions">
                        @foreach(config('monitoring.vehicle_types') as $type)
                            <option value="{{ $type }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Merk</label>
                    <input type="text" class="form-control" name="merk" value="{{ old('merk', $vehicle->merk) }}" placeholder="Toyota Avanza">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <input type="number" class="form-control" name="tahun" min="1980" max="{{ now()->year + 1 }}" value="{{ old('tahun', $vehicle->tahun) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="status">
                        @foreach(config('monitoring.vehicle_statuses') as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $vehicle->status ?? 'aktif') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Driver (Nama Bebas)</label>
                    <input type="text" class="form-control" name="driver" value="{{ old('driver', $vehicle->driver) }}" placeholder="Nama driver">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Driver (Akun User)</label>
                    <select class="form-select" name="driver_id">
                        <option value="">— Tidak Terhubung Akun —</option>
                        @foreach($driverOptions as $d)
                            <option value="{{ $d->id }}" @selected((int) old('driver_id', $vehicle->driver_id) === $d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hubungkan ke akun user dengan role Driver agar muncul di dashboard "Kendaraan Saya".</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan', $vehicle->keterangan) }}</textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
            Simpan
        </button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('vehicle.index') }}">Batal</a>
    </div>
</form>

@endsection
