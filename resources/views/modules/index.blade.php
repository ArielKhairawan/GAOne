@extends('layouts.app')

@section('title', $meta['title'])
@section('page-title', $meta['title'])
@section('page-subtitle', 'Kelola data ' . $meta['title'])

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Modules · {{ $meta['title'] }}</span>
        <h1 class="section-title">{{ $meta['title'] }}</h1>
        <p class="section-subtitle">Total {{ $records->total() }} data tersedia.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('modules.create', $meta['slug']) }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Tambah
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#ID</th>
                        @foreach(array_keys($meta['fields']) as $field)
                            <th>{{ str($field)->headline() }}</th>
                        @endforeach
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td style="font-size:12.5px; color:var(--text-3); font-variant-numeric:tabular-nums; font-weight:600">
                            #{{ str_pad($record->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        @foreach(array_keys($meta['fields']) as $field)
                        <td>
                            @if($field === 'status')
                                @php $s = data_get($record, $field); @endphp
                                <span class="status-badge {{ in_array($s, ['approved','completed']) ? 'active' : (in_array($s, ['rejected']) ? 'inactive' : 'pending') }}">
                                    {{ ucfirst($s ?? '—') }}
                                </span>
                            @elseif(in_array($field, ['departure_date','return_date','po_date','starts_at','ends_at','sent_at','completed_at','realized_at']) || str_ends_with($field, '_at') || str_ends_with($field, '_date'))
                                <span style="color:var(--text-3); font-size:13px">
                                    {{ optional(is_string(data_get($record, $field)) ? \Carbon\Carbon::parse(data_get($record, $field)) : data_get($record, $field))?->format('d M Y') ?? '—' }}
                                </span>
                            @elseif(in_array($field, ['estimated_cost','total_amount','ticket_cost','hotel_cost','transport_cost','daily_allowance','other_cost']) || str_contains($field, '_cost') || str_contains($field, '_amount'))
                                <span style="font-variant-numeric:tabular-nums">
                                    Rp {{ number_format((float) data_get($record, $field), 0, ',', '.') }}
                                </span>
                            @elseif(str_starts_with($field, 'is_'))
                                <span class="status-badge {{ data_get($record, $field) ? 'active' : 'inactive' }}">
                                    {{ data_get($record, $field) ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            @else
                                <span style="color:var(--text-2)">{{ data_get($record, $field) ?? '—' }}</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('modules.edit', [$meta['slug'], $record->id]) }}">
                                    Edit
                                </a>

                                @if(in_array($record->status ?? null, ['draft', 'revision'], true))
                                <form class="d-inline" method="post"
                                      action="{{ route('modules.submit', [$meta['slug'], $record->id]) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">Submit</button>
                                </form>
                                @endif

                                <form class="d-inline" method="post"
                                      action="{{ route('modules.destroy', [$meta['slug'], $record->id]) }}"
                                      onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($meta['fields']) + 2 }}"
                            style="text-align:center; padding:48px; color:var(--text-3); font-size:14px">
                            Belum ada data. Klik <strong>Tambah</strong> untuk membuat entri pertama.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $records->links() }}
</div>

@endsection
