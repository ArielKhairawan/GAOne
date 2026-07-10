@extends('layouts.app')

@section('title', 'Beri Rating')
@section('page-title', 'Beri Rating Kepuasan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Layanan</span>
        <h1 class="section-title">{{ $moduleLabels[$survey->service_type] ?? $survey->service_type }}</h1>
        <p class="section-subtitle">Bagaimana pengalaman Anda dengan layanan ini?</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('csat.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">{{ $errors->first() }}</div>
@endif

<form method="post" action="{{ route('csat.store', $survey) }}">
    @csrf
    <div class="card mb-4">
        <div class="card-body p-4">
            <label class="form-label">Rating <span style="color:var(--crimson)">*</span></label>
            <div class="d-flex gap-3 mb-4">
                @foreach($ratingLabels as $value => $label)
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="rating" id="rating-{{ $value }}" value="{{ $value }}" @checked(old('rating') == $value)>
                    <label class="form-check-label" for="rating-{{ $value }}">{{ $value }} - {{ $label }}</label>
                </div>
                @endforeach
            </div>

            <label class="form-label">Komentar (opsional)</label>
            <textarea class="form-control" name="comments" rows="4">{{ old('comments') }}</textarea>
        </div>
    </div>

    <button class="btn btn-primary px-5">Kirim Penilaian</button>
</form>

@endsection
