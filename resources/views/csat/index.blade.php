@extends('layouts.app')

@section('title', 'Survei Kepuasan')
@section('page-title', 'Survei Kepuasan Pengguna (CSAT)')

@section('content')

<div class="mb-5">
    <span class="section-eyebrow">Layanan</span>
    <h1 class="section-title">Survei Kepuasan Pengguna</h1>
    <p class="section-subtitle">Bantu kami meningkatkan layanan dengan mengisi survei singkat ini.</p>
</div>

@if($pending->isNotEmpty())
<div class="card mb-4">
    <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Menunggu Penilaian Anda</div></div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead><tr><th>Modul</th><th>Tanggal</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($pending as $survey)
                <tr>
                    <td>{{ $moduleLabels[$survey->service_type] ?? $survey->service_type }}</td>
                    <td>{{ $survey->sent_at?->format('d M Y') }}</td>
                    <td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('csat.show', $survey) }}">Beri Rating</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info mb-4">Tidak ada survei yang menunggu penilaian Anda saat ini.</div>
@endif

<div class="card">
    <div style="padding:20px 24px; border-bottom:1px solid var(--border)"><div style="font-size:14px; font-weight:600">Riwayat Survei Anda</div></div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead><tr><th>Modul</th><th>Rating</th><th>Komentar</th><th>Tanggal</th></tr></thead>
            <tbody>
                @forelse($history as $survey)
                <tr>
                    <td>{{ $moduleLabels[$survey->service_type] ?? $survey->service_type }}</td>
                    <td>{{ $survey->response->satisfaction_score ?? '—' }}/5 — {{ $ratingLabels[$survey->response->satisfaction_score] ?? '' }}</td>
                    <td>{{ $survey->response->comments ?? '—' }}</td>
                    <td>{{ $survey->completed_at?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; padding:32px; color:var(--text-3)">Belum ada riwayat survei.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
