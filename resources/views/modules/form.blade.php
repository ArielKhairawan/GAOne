@extends('layouts.app')

@section('title', ($record->exists ? 'Edit' : 'Tambah') . ' ' . $meta['title'])
@section('page-title', ($record->exists ? 'Edit' : 'Tambah') . ' ' . $meta['title'])
@section('page-subtitle', 'Isi data formulir dengan benar dan lengkap')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">{{ $meta['title'] }}</span>
        <h1 class="section-title">{{ $record->exists ? 'Edit Data' : 'Tambah Data Baru' }}</h1>
        <p class="section-subtitle">
            {{ $record->exists
                ? 'Perbarui informasi entri #' . str_pad($record->id, 4, '0', STR_PAD_LEFT)
                : 'Lengkapi semua kolom yang diperlukan.' }}
        </p>
    </div>

<a class="btn btn-outline-secondary" href="{{ route('modules.index', $meta['slug']) }}">
    <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
    </svg>
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

<form method="post"
      action="{{ $record->exists
            ? route('modules.update', [$meta['slug'], $record->id])
            : route('modules.store', $meta['slug']) }}">

@csrf

@if($record->exists)
    @method('PUT')
@endif

<div class="card mb-4">
    <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
        <div style="font-size:14px; font-weight:600; color:var(--text)">
            Informasi {{ $meta['title'] }}
        </div>
    </div>

    <div class="card-body p-4">
        <div class="row g-3">

            @foreach($meta['fields'] as $field => $rule)

            <div class="col-md-6">
                <label class="form-label">
                    {{ str($field)->headline() }}

                    @if(str_contains($rule, 'required'))
                        <span style="color:var(--crimson)">*</span>
                    @endif
                </label>

                @if(str_contains($rule, 'boolean'))

                    <select class="form-select" name="{{ $field }}">
                        <option value="1" @selected(old($field, $record->$field) == 1)>Ya</option>
                        <option value="0" @selected(old($field, $record->$field) == 0 || old($field, $record->$field) === null)>Tidak</option>
                    </select>

                @elseif(str_contains($rule, 'in:') && preg_match('/in:([^|]+)/', $rule, $m))

                    <select class="form-select" name="{{ $field }}">
                        <option value="">— Pilih —</option>

                        @foreach(explode(',', $m[1]) as $opt)
                            <option value="{{ $opt }}" @selected(old($field, $record->$field) === $opt)>
                                {{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>

                @elseif(str_contains($rule, 'exists:') && preg_match('/exists:(\w+),/', $rule, $m))

                    @php
                        $table = $m[1];

                        try {
                            $options = \Illuminate\Support\Facades\DB::table($table)
                                ->select('id', 'name')
                                ->orderBy('name')
                                ->get();
                        } catch (\Exception $e) {
                            $options = collect();
                        }
                    @endphp

                    <select class="form-select" name="{{ $field }}">
                        <option value="">— Pilih —</option>

                        @foreach($options as $opt)
                            <option value="{{ $opt->id }}"
                                    @selected(old($field, $record->$field) == $opt->id)>
                                {{ $opt->name ?? $opt->id }}
                            </option>
                        @endforeach
                    </select>

                @elseif(str_contains($rule, 'date'))

                    @php
                        $isDatetime =
                            str_contains($field, '_at') ||
                            in_array($field, [
                                'starts_at',
                                'ends_at',
                                'sent_at',
                                'completed_at',
                                'realized_at'
                            ]);

                        $fmt = $isDatetime ? 'Y-m-d\TH:i' : 'Y-m-d';
                        $type = $isDatetime ? 'datetime-local' : 'date';

                        $val = old(
                            $field,
                            optional(
                                $record->$field instanceof \Carbon\Carbon
                                    ? $record->$field
                                    : (
                                        is_string($record->$field)
                                            ? \Carbon\Carbon::parse($record->$field)
                                            : null
                                    )
                            )->format($fmt)
                        );
                    @endphp

                    <input class="form-control"
                           type="{{ $type }}"
                           name="{{ $field }}"
                           value="{{ $val }}">

                @elseif(str_contains($rule, 'numeric'))

                    <input class="form-control"
                           type="number"
                           step="any"
                           min="0"
                           name="{{ $field }}"
                           value="{{ old($field, $record->$field) }}">

                @elseif(
                    str_contains($rule, 'string') &&
                    (
                        str_contains($field, 'notes') ||
                        str_contains($field, 'description') ||
                        str_contains($field, 'purpose') ||
                        str_contains($field, 'body') ||
                        str_contains($field, 'address')
                    )
                )

                    <textarea class="form-control"
                              name="{{ $field }}"
                              rows="3"
                              placeholder="{{ str($field)->headline() }}...">{{ old($field, $record->$field) }}</textarea>

                @else

                    <input class="form-control"
                           name="{{ $field }}"
                           value="{{ old($field, $record->$field) }}"
                           placeholder="{{ str($field)->headline() }}">

                @endif

                @error($field)
                    <div style="font-size:12px; color:var(--crimson); margin-top:4px">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            @endforeach

            {{-- Rincian Booking --}}

            <div class="col-12">
                <hr class="my-4">
                <h5>Rincian Booking</h5>
            </div>

            <div class="col-md-6">
                <label class="form-label">Kapasitas Peserta</label>
                <input type="number"
                       class="form-control"
                       name="capacity"
                       value="{{ old('capacity', $record->capacity ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Konsumsi</label>
                <select class="form-select" name="has_consumption">
                    <option value="0" @selected(old('has_consumption', $record->has_consumption ?? 0) == 0)>
                        Tidak Ada
                    </option>
                    <option value="1" @selected(old('has_consumption', $record->has_consumption ?? 0) == 1)>
                        Ada
                    </option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Jenis Konsumsi</label>
                <select class="form-select" name="consumption_type">
                    <option value="">Pilih</option>

                    @foreach([
                        'makan_siang' => 'Makan Siang',
                        'snack_kering' => 'Snack Kering',
                        'snack_basah' => 'Snack Basah',
                        'kombinasi' => 'Kombinasi'
                    ] as $val => $label)

                        <option value="{{ $val }}"
                                @selected(old('consumption_type', $record->consumption_type ?? '') === $val)>
                            {{ $label }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Detail Menu / Kue</label>
                <textarea class="form-control"
                          name="menu_detail"
                          rows="3"
                          placeholder="Contoh: Risol, Lapis Legit, Brownies, Air Mineral">{{ old('menu_detail', $record->menu_detail ?? '') }}</textarea>
            </div>

        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary px-5">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
        </svg>
        Simpan
    </button>

    <a class="btn btn-outline-secondary px-4"
       href="{{ route('modules.index', $meta['slug']) }}">
        Batal
    </a>
</div>

</form>

@endsection
