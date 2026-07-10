@extends('layouts.app')

@section('title', ($fuelLog->exists ? 'Edit' : 'Tambah') . ' Data BBM')
@section('page-title', ($fuelLog->exists ? 'Edit' : 'Tambah') . ' Data Pengisian BBM')
@section('page-subtitle', 'Lengkapi data pengisian bahan bakar')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Monitoring Bahan Bakar</span>
        <h1 class="section-title">{{ $fuelLog->exists ? 'Edit Data BBM' : 'Tambah Data BBM' }}</h1>
        <p class="section-subtitle">Kilometer akhir dan jumlah liter digunakan untuk menghitung konsumsi otomatis.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('fuel.index') }}">
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

<form method="post" action="{{ $fuelLog->exists ? route('fuel.update', $fuelLog) : route('fuel.store') }}">
    @csrf
    @if($fuelLog->exists) @method('PUT') @endif

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
            <div style="font-size:14px; font-weight:600; color:var(--text)">Informasi Pengisian</div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Kendaraan <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="vehicle_id">
                        <option value="">— Pilih Kendaraan —</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" @selected((int) old('vehicle_id', $fuelLog->vehicle_id) === $v->id)>{{ $v->plat_nomor }} — {{ $v->jenis_kendaraan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Pengisian <span style="color:var(--crimson)">*</span></label>
                    <input type="date" class="form-control" name="tanggal_pengisian"
                           value="{{ old('tanggal_pengisian', optional($fuelLog->tanggal_pengisian)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Driver (Nama Bebas)</label>
                    <input type="text" class="form-control" name="driver" value="{{ old('driver', $fuelLog->driver) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Driver (Akun User)</label>
                    <select class="form-select" name="driver_id">
                        <option value="">— Tidak Terhubung Akun —</option>
                        @foreach($driverOptions as $d)
                            <option value="{{ $d->id }}" @selected((int) old('driver_id', $fuelLog->driver_id) === $d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jenis Bahan Bakar <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="jenis_bahan_bakar" list="fuelTypeOptions" value="{{ old('jenis_bahan_bakar', $fuelLog->jenis_bahan_bakar) }}">
                    <datalist id="fuelTypeOptions">
                        @foreach($fuelTypes as $type)
                            <option value="{{ $type }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Harga per Liter (Rp) <span style="color:var(--crimson)">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control calc-input" id="hargaPerLiter" name="harga_per_liter" value="{{ old('harga_per_liter', $fuelLog->harga_per_liter) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jumlah Liter <span style="color:var(--crimson)">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control calc-input" id="jumlahLiter" name="jumlah_liter" value="{{ old('jumlah_liter', $fuelLog->jumlah_liter) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kilometer Awal <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="0" class="form-control calc-input" id="kmAwal" name="kilometer_awal" value="{{ old('kilometer_awal', $fuelLog->kilometer_awal) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kilometer Akhir <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="0" class="form-control calc-input" id="kmAkhir" name="kilometer_akhir" value="{{ old('kilometer_akhir', $fuelLog->kilometer_akhir) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estimasi (otomatis saat disimpan)</label>
                    <div class="form-control" style="background:var(--surface-2); display:flex; flex-direction:column; gap:2px; height:auto">
                        <span id="previewTotal" class="small text-muted">Total: —</span>
                        <span id="previewKonsumsi" class="small text-muted">Konsumsi: —</span>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan', $fuelLog->keterangan) }}</textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
            Simpan
        </button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('fuel.index') }}">Batal</a>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var hargaPerLiter = document.getElementById('hargaPerLiter');
    var jumlahLiter = document.getElementById('jumlahLiter');
    var kmAwal = document.getElementById('kmAwal');
    var kmAkhir = document.getElementById('kmAkhir');
    var previewTotal = document.getElementById('previewTotal');
    var previewKonsumsi = document.getElementById('previewKonsumsi');

    function formatRupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function recalc() {
        var harga = parseFloat(hargaPerLiter.value) || 0;
        var liter = parseFloat(jumlahLiter.value) || 0;
        var awal = parseInt(kmAwal.value, 10) || 0;
        var akhir = parseInt(kmAkhir.value, 10) || 0;
        var jarak = Math.max(0, akhir - awal);

        previewTotal.textContent = 'Total: ' + formatRupiah(harga * liter);
        previewKonsumsi.textContent = 'Konsumsi: ' + (liter > 0 ? (jarak / liter).toFixed(2) : '—') + ' km/liter (jarak ' + jarak + ' km)';
    }

    [hargaPerLiter, jumlahLiter, kmAwal, kmAkhir].forEach(function (el) {
        el.addEventListener('input', recalc);
    });

    recalc();
});
</script>
@endpush
