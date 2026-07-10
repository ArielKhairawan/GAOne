@extends('layouts.app')

@section('title', 'Buat Permintaan ATK')
@section('page-title', 'Buat Permintaan ATK')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Inventaris</span>
        <h1 class="section-title">Buat Permintaan ATK</h1>
        <p class="section-subtitle">Permintaan akan diproses oleh GA Staff, lalu disetujui oleh Manager.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('atk.requests.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ route('atk.requests.store') }}">
    @csrf

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Departemen <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="department" value="{{ old('department', auth()->user()->department) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
            <div style="font-size:14px; font-weight:600; color:var(--text)">Daftar Barang</div>
            <button type="button" id="addRow" class="btn btn-sm btn-outline-primary">+ Tambah Baris</button>
        </div>
        <div class="card-body p-4">
            <div id="itemRows"></div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Ajukan Permintaan</button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('atk.requests.index') }}">Batal</a>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var items = @json($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name.' (stok: '.$i->stock.' '.$i->satuan.')']));
    var container = document.getElementById('itemRows');
    var rowIndex = 0;

    function buildOptions(selected) {
        return items.map(function (it) {
            var sel = String(it.id) === String(selected) ? 'selected' : '';
            return '<option value="' + it.id + '" ' + sel + '>' + it.name + '</option>';
        }).join('');
    }

    function addRow() {
        var idx = rowIndex++;
        var row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2';
        row.innerHTML =
            '<div class="col-md-7">' +
                '<select class="form-select" name="items[' + idx + '][atk_item_id]">' +
                    '<option value="">— Pilih Barang —</option>' + buildOptions(null) +
                '</select>' +
            '</div>' +
            '<div class="col-md-3">' +
                '<input type="number" min="1" class="form-control" name="items[' + idx + '][quantity]" placeholder="Jumlah">' +
            '</div>' +
            '<div class="col-md-2">' +
                '<button type="button" class="btn btn-outline-danger btn-sm removeRow" style="width:100%">Hapus</button>' +
            '</div>';
        container.appendChild(row);
        row.querySelector('.removeRow').addEventListener('click', function () { row.remove(); });
    }

    document.getElementById('addRow').addEventListener('click', addRow);
    addRow();
});
</script>
@endpush
