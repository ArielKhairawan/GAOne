@extends('layouts.app')

@section('title', $type === 'masuk' ? 'Barang Masuk' : 'Barang Keluar')
@section('page-title', $type === 'masuk' ? 'Catat Barang Masuk' : 'Catat Barang Keluar')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Inventaris ATK</span>
        <h1 class="section-title">{{ $type === 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route($type === 'masuk' ? 'atk.stock-in.index' : 'atk.stock-out.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ route($type === 'masuk' ? 'atk.stock-in.store' : 'atk.stock-out.store') }}">
    @csrf
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Barang <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="atk_item_id">
                        <option value="">— Pilih Barang —</option>
                        @foreach($items as $i)
                            <option value="{{ $i->id }}" @selected((int) old('atk_item_id') === $i->id)>
                                {{ $i->name }} (stok: {{ $i->stock }} {{ $i->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="1" class="form-control" name="quantity" value="{{ old('quantity') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Simpan</button>
    </div>
</form>

@endsection
