@extends('layouts.app')

@section('title', 'Approvals')
@section('page-title', 'Approval Monitoring')
@section('page-subtitle', 'Pantau dan tindaklanjuti permintaan yang membutuhkan persetujuan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Workflow</span>
        <h1 class="section-title">Approval Monitoring</h1>
        <p class="section-subtitle">{{ $pending->total() }} permintaan menunggu tindakan Anda.</p>
    </div>
</div>

{{-- Pending Approvals --}}
<div class="card mb-5">
    <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between">
        <div style="font-size:14px; font-weight:600; color:var(--text)">Menunggu Persetujuan</div>
        <span class="status-badge pending">{{ $pending->total() }} pending</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Modul</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $approval)
                    <tr>
                        <td style="font-size:12.5px; color:var(--text-3); font-variant-numeric:tabular-nums">
                            #{{ str_pad($approval->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="fw-medium">{{ class_basename($approval->approvable_type) }}</td>
                        <td>
                            <span class="status-badge pending">{{ ucfirst($approval->status) }}</span>
                        </td>
                        <td style="color:var(--text-3); font-size:13px">
                            {{ optional($approval->submitted_at)->diffForHumans() ?? '—' }}
                        </td>
                        <td class="text-end">
                            <div class="approval-actions" style="justify-content:flex-end">
                                <form class="d-inline" method="POST" action="{{ route('approvals.act', $approval) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-sm btn-outline-success">Setujui</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('approvals.act', $approval) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-sm btn-outline-danger">Tolak</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('approvals.act', $approval) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="revision">
                                    <button class="btn btn-sm btn-outline-warning">Revisi</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px; color:var(--text-3); font-size:14px">
                            Tidak ada permintaan yang menunggu. 🎉
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 mb-5">{{ $pending->links() }}</div>

{{-- History --}}
<div>
    <h2 class="h5" style="margin-bottom:16px; color:var(--text)">Riwayat Approval</h2>
    <div class="card">
        <div class="card-body p-0">
            <ul class="list-group">
                @forelse($history as $item)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span style="font-size:12px; color:var(--text-4); font-variant-numeric:tabular-nums">
                                #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="fw-medium" style="margin-left:10px">{{ class_basename($item->approvable_type ?? 'Item') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="status-badge {{ $item->status === 'approved' ? 'active' : 'inactive' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                            <span style="font-size:12.5px; color:var(--text-3)">
                                {{ optional($item->completed_at)->format('d M Y, H:i') ?? '—' }}
                            </span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="list-group-item" style="text-align:center; color:var(--text-3)">Belum ada riwayat.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@endsection
