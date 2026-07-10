@extends('layouts.app')

@section('title', ($inspection->exists ? 'Edit' : 'Tambah') . ' Inspeksi WC')
@section('page-title', ($inspection->exists ? 'Edit' : 'Tambah') . ' Inspeksi Kebersihan WC')
@section('page-subtitle', 'Lengkapi checklist pemeriksaan kebersihan')

@php
    $existingItems = $inspection->exists ? $inspection->items->pluck('status', 'item_name') : collect();
@endphp

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Monitoring Kebersihan WC</span>
        <h1 class="section-title">{{ $inspection->exists ? 'Edit Inspeksi' : 'Tambah Inspeksi' }}</h1>
        <p class="section-subtitle">Checklist dapat bertambah dari waktu ke waktu melalui konfigurasi, tanpa mengubah database.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('toilet.index') }}">
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

<form method="post" action="{{ $inspection->exists ? route('toilet.update', $inspection) : route('toilet.store') }}" enctype="multipart/form-data">
    @csrf
    @if($inspection->exists) @method('PUT') @endif

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
            <div style="font-size:14px; font-weight:600; color:var(--text)">Informasi Inspeksi</div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Tanggal <span style="color:var(--crimson)">*</span></label>
                    <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', optional($inspection->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jam <span style="color:var(--crimson)">*</span></label>
                    <input type="time" class="form-control" name="jam" value="{{ old('jam', $inspection->jam ?? now()->format('H:i')) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Lokasi <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="lokasi" id="lokasiSelect">
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" @selected(old('lokasi', $inspection->lokasi) === $loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3" id="lokasiDetailWrap">
                    <label class="form-label">Detail Lokasi</label>
                    <input type="text" class="form-control" name="lokasi_detail" value="{{ old('lokasi_detail', $inspection->lokasi_detail) }}" placeholder="Cth: Lantai 2 - Dekat Lobby">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Petugas <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="petugas_id">
                        <option value="">— Pilih Petugas —</option>
                        @foreach($petugasOptions as $p)
                            <option value="{{ $p->id }}" @selected((int) old('petugas_id', $inspection->petugas_id ?? auth()->id()) === $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status Kebersihan <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="status">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $inspection->status ?? 'bersih') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Upload Foto <span style="color:var(--crimson)">*</span> <small class="text-muted">(wajib, jpg/jpeg/png/webp, maks 5MB)</small></label>
                    <input type="file" class="form-control" name="foto" accept="image/jpeg,image/png,image/webp" {{ $inspection->exists ? '' : 'required' }}>
                    @if($inspection->foto)
                        <small class="text-muted">Foto saat ini tersimpan. Unggah file baru untuk mengganti (opsional saat edit).</small>
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">Catatan Temuan</label>
                    <textarea class="form-control" name="catatan" rows="3">{{ old('catatan', $inspection->catatan) }}</textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
            <div style="font-size:14px; font-weight:600; color:var(--text)">Checklist Kebersihan</div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @foreach($checklistItems as $item)
                <div class="col-md-4">
                    <label class="form-label">{{ $item }}</label>
                    <select class="form-select" name="items[{{ $item }}]">
                        @foreach($itemStatuses as $value => $label)
                            <option value="{{ $value }}" @selected(old("items.$item", $existingItems->get($item, 'baik')) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
            <div style="font-size:14px; font-weight:600; color:var(--text)">Tanda Tangan Petugas</div>
        </div>
        <div class="card-body p-4">
            <canvas id="signaturePad" width="500" height="160" style="border:1px solid var(--border); border-radius:8px; width:100%; max-width:500px; touch-action:none; cursor:crosshair"></canvas>
            <input type="hidden" name="tanda_tangan" id="tandaTanganInput" value="{{ old('tanda_tangan', $inspection->tanda_tangan) }}">
            <div class="d-flex gap-2 mt-2">
                <button type="button" id="clearSignature" class="btn btn-outline-secondary btn-sm">Hapus Tanda Tangan</button>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
            Simpan
        </button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('toilet.index') }}">Batal</a>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tampilkan kolom Detail Lokasi hanya jika lokasi = "Lokasi Lainnya"
    var lokasiSelect = document.getElementById('lokasiSelect');
    var lokasiDetailWrap = document.getElementById('lokasiDetailWrap');

    function toggleLokasiDetail() {
        lokasiDetailWrap.style.display = lokasiSelect.value === 'Lokasi Lainnya' ? '' : 'none';
    }
    lokasiSelect.addEventListener('change', toggleLokasiDetail);
    toggleLokasiDetail();

    // Signature pad sederhana berbasis canvas (tanpa dependency eksternal)
    var canvas = document.getElementById('signaturePad');
    var ctx = canvas.getContext('2d');
    var input = document.getElementById('tandaTanganInput');
    var drawing = false;

    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#111827';

    if (input.value) {
        var img = new Image();
        img.onload = function () { ctx.drawImage(img, 0, 0, canvas.width, canvas.height); };
        img.src = input.value;
    }

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var point = e.touches ? e.touches[0] : e;
        return {
            x: (point.clientX - rect.left) * scaleX,
            y: (point.clientY - rect.top) * scaleY,
        };
    }

    function start(e) {
        drawing = true;
        var pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        e.preventDefault();
    }

    function move(e) {
        if (!drawing) return;
        var pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        e.preventDefault();
    }

    function end() {
        if (!drawing) return;
        drawing = false;
        input.value = canvas.toDataURL('image/png');
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    canvas.addEventListener('mouseup', end);
    canvas.addEventListener('mouseleave', end);
    canvas.addEventListener('touchstart', start);
    canvas.addEventListener('touchmove', move);
    canvas.addEventListener('touchend', end);

    document.getElementById('clearSignature').addEventListener('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        input.value = '';
    });
});
</script>
@endpush
