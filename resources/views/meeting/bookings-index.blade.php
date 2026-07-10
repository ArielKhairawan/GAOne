@extends('layouts.app')

@section('title', 'Booking Ruang Meeting')
@section('page-title', 'Booking Ruang Meeting')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">Booking Ruang Meeting</h1>
        <p class="section-subtitle">Total {{ $bookings->total() }} booking.</p>
    </div>
    @can('meeting.create')
    <a class="btn btn-primary" href="{{ route('meeting.bookings.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Buat Booking
    </a>
    @endcan
</div>

<form method="get" class="card mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" placeholder="Dari tanggal">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-3">
                <select name="meeting_room_id" class="form-select">
                    <option value="">— Semua Ruangan —</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" @selected(($filters['meeting_room_id'] ?? '') == $room->id)>{{ $room->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary" style="width:100%; justify-content:center">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Tanggal</th><th>Jam</th><th>Ruangan</th><th>Kegiatan</th><th>Pemohon</th><th>Peserta</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td>{{ $b->tanggal->format('d M Y') }}</td>
                        <td>{{ $b->jam_mulai }} - {{ $b->jam_selesai }}</td>
                        <td class="fw-medium">{{ $b->room->nama_ruangan ?? '—' }}</td>
                        <td>{{ $b->nama_kegiatan }}</td>
                        <td>{{ $b->requester->name ?? '—' }}</td>
                        <td>{{ $b->jumlah_peserta }}</td>
                        <td>
                            <span class="status-badge {{ in_array($b->status, ['approved','selesai']) ? 'active' : ($b->status === 'rejected' ? 'inactive' : 'pending') }}">
                                {{ $statusLabels[$b->status] ?? $b->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('meeting.bookings.show', $b) }}">Lihat</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center; padding:48px; color:var(--text-3)">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">{{ $bookings->links() }}</div>

@endsection
