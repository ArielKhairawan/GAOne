@extends('layouts.app')

@section('title', 'Master Ruangan')
@section('page-title', 'Master Ruangan Meeting')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Meeting & Konsumsi</span>
        <h1 class="section-title">Master Ruangan</h1>
        <p class="section-subtitle">Total {{ $rooms->total() }} ruangan.</p>
    </div>
    @can('meeting.create')
    <a class="btn btn-primary" href="{{ route('meeting.rooms.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Tambah Ruangan
    </a>
    @endcan
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Kode</th><th>Nama Ruangan</th><th>Lokasi</th><th>Kapasitas</th><th>Fasilitas</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td>{{ $room->kode_ruangan }}</td>
                        <td class="fw-medium">{{ $room->nama_ruangan }}</td>
                        <td>{{ $room->lokasi ?? '—' }}</td>
                        <td>{{ $room->kapasitas }} orang</td>
                        <td style="font-size:12px">{{ implode(', ', $room->fasilitas ?? []) ?: '—' }}</td>
                        <td>
                            <span class="status-badge {{ $room->status === 'tersedia' ? 'active' : ($room->status === 'tidak_aktif' ? 'inactive' : 'pending') }}">
                                {{ config('monitoring.meeting_room_statuses')[$room->status] }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                @can('meeting.edit')
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('meeting.rooms.edit', $room) }}">Edit</a>
                                @endcan
                                @can('meeting.delete')
                                <form method="post" action="{{ route('meeting.rooms.destroy', $room) }}" onsubmit="return confirm('Hapus ruangan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; padding:48px; color:var(--text-3)">Belum ada data ruangan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">{{ $rooms->links() }}</div>

@endsection
