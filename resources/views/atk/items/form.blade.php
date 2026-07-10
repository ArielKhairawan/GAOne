@extends('layouts.app')

@section('title', ($item->exists ? 'Edit' : 'Tambah') . ' Barang ATK')
@section('page-title', ($item->exists ? 'Edit' : 'Tambah') . ' Barang ATK')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Inventaris ATK</span>
        <h1 class="section-title">{{ $item->exists ? 'Edit Barang' : 'Tambah Barang' }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('atk.items.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ $item->exists ? route('atk.items.update', $item) : route('atk.items.store') }}">
    @csrf
    @if($item->exists) @method('PUT') @endif

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Barang <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="code" value="{{ old('code', $item->code) }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Barang <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $item->name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="atk_category_id">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((int) old('atk_category_id', $item->atk_category_id) === $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Satuan <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="satuan" list="satuanOptions" value="{{ old('satuan', $item->satuan ?? 'pcs') }}">
                    <datalist id="satuanOptions">
                        @foreach(config('monitoring.atk_units') as $unit)<option value="{{ $unit }}">@endforeach
                    </datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lokasi Penyimpanan</label>
                    <input type="text" class="form-control" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan', $item->lokasi_penyimpanan) }}">
                </div>

                @if(! $item->exists)
                <div class="col-md-4">
                    <label class="form-label">Stok Awal <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="0" class="form-control" name="stock" value="{{ old('stock', 0) }}">
                </div>
                @endif

                <div class="col-md-4">
                    <label class="form-label">Stok Minimum <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="0" class="form-control" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock ?? 0) }}">
                </div>
            </div>

            @if($item->exists)
            <p class="small text-muted mt-3 mb-0">
                Stok saat ini: <strong>{{ $item->stock }} {{ $item->satuan }}</strong>.
                Gunakan menu Barang Masuk / Barang Keluar untuk mengubah jumlah stok.
            </p>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Simpan</button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('atk.items.index') }}">Batal</a>
    </div>
</form>

@endsection
