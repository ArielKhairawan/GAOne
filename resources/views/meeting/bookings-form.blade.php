@extends('layouts.app')

@section('title', 'Buat Booking Ruang Meeting')
@section('page-title', 'Buat Booking Ruang Meeting')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">Buat Booking Ruang Meeting</h1>
        <p class="section-subtitle">Booking akan diperiksa otomatis untuk bentrok jadwal sebelum diajukan.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('meeting.bookings.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ route('meeting.bookings.store') }}">
    @csrf

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Detail Booking</div></div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Ruangan <span style="color:var(--crimson)">*</span></label>
                    <select class="form-select" name="meeting_room_id" id="roomSelect">
                        <option value="">— Pilih Ruangan —</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" data-kapasitas="{{ $room->kapasitas }}" @selected(old('meeting_room_id') == $room->id)>
                                {{ $room->nama_ruangan }} (kapasitas {{ $room->kapasitas }} orang)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Kegiatan <span style="color:var(--crimson)">*</span></label>
                    <input type="text" class="form-control" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal <span style="color:var(--crimson)">*</span></label>
                    <input type="date" class="form-control" name="tanggal" id="tanggalInput" min="{{ now()->format('Y-m-d') }}" value="{{ old('tanggal') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Mulai <span style="color:var(--crimson)">*</span></label>
                    <input type="time" class="form-control" name="jam_mulai" id="jamMulaiInput" value="{{ old('jam_mulai') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Selesai <span style="color:var(--crimson)">*</span></label>
                    <input type="time" class="form-control" name="jam_selesai" id="jamSelesaiInput" value="{{ old('jam_selesai') }}">
                </div>
                <div class="col-12">
                    <div id="availabilityNotice"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Departemen</label>
                    <input type="text" class="form-control" name="departemen" value="{{ old('departemen', auth()->user()->department) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah Peserta <span style="color:var(--crimson)">*</span></label>
                    <input type="number" min="1" class="form-control" name="jumlah_peserta" id="pesertaInput" value="{{ old('jumlah_peserta') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="catatan" rows="2">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="butuh_konsumsi" id="butuhKonsumsi" value="1" @checked(old('butuh_konsumsi'))>
                <label class="form-check-label" for="butuhKonsumsi">Butuh Konsumsi?</label>
            </div>

            <div id="consumptionFields" style="display:none; margin-top:16px">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Jenis Konsumsi</label>
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
                        <textarea class="form-control" name="detail_konsumsi" rows="2" placeholder="Cth: Nasi Kotak Ayam Bakar, Air Mineral">{{ old('detail_konsumsi') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Ajukan Booking</button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('meeting.bookings.index') }}">Batal</a>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var butuhKonsumsi = document.getElementById('butuhKonsumsi');
    var consumptionFields = document.getElementById('consumptionFields');

    function toggleConsumption() {
        consumptionFields.style.display = butuhKonsumsi.checked ? '' : 'none';
    }
    butuhKonsumsi.addEventListener('change', toggleConsumption);
    toggleConsumption();

    var roomSelect = document.getElementById('roomSelect');
    var tanggalInput = document.getElementById('tanggalInput');
    var jamMulaiInput = document.getElementById('jamMulaiInput');
    var jamSelesaiInput = document.getElementById('jamSelesaiInput');
    var notice = document.getElementById('availabilityNotice');

    function checkAvailability() {
        var roomId = roomSelect.value, tanggal = tanggalInput.value, jamMulai = jamMulaiInput.value, jamSelesai = jamSelesaiInput.value;
        notice.innerHTML = '';
        if (! roomId || ! tanggal || ! jamMulai || ! jamSelesai || jamSelesai <= jamMulai) {
            return;
        }
        var url = '{{ route('meeting.bookings.check-availability') }}?meeting_room_id=' + roomId + '&tanggal=' + tanggal + '&jam_mulai=' + jamMulai + '&jam_selesai=' + jamSelesai;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.available) {
                    notice.innerHTML = '<div class="alert alert-success">Ruangan tersedia pada jadwal ini.</div>';
                } else {
                    notice.innerHTML = '<div class="alert alert-danger">Ruangan bentrok dengan booking lain pada jadwal ini. Silakan pilih jam atau ruangan lain.</div>';
                }
            })
            .catch(function () {});
    }

    [roomSelect, tanggalInput, jamMulaiInput, jamSelesaiInput].forEach(function (el) {
        el.addEventListener('change', checkAvailability);
    });
});
</script>
@endpush
