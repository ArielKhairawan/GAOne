@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('page-subtitle', 'Kelola akun, peran, dan status pengguna sistem')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">System · Users</span>
        <h1 class="section-title">Manajemen Pengguna</h1>
        <p class="section-subtitle">Kelola akun, peran, dan hak akses seluruh pengguna.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-success" href="{{ route('users.export') }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            Export CSV
        </a>
        <a class="btn btn-primary" href="{{ route('users.create') }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Tambah User
        </a>
    </div>
</div>

<div class="card user-table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="fw-medium">{{ $user->name }}</td>
                        <td style="color:var(--text-3)">{{ $user->email }}</td>
                        <td>{{ $user->department }}</td>
                        <td>{{ $user->position }}</td>
                        <td>
                            <span style="font-size:12.5px; background:var(--surface-3); color:var(--text-2); padding:3px 8px; border-radius:6px; font-weight:500">
                                {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="status-badge active">Aktif</span>
                            @else
                                <span class="status-badge inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('users.edit', $user) }}">
                                    Edit
                                </a>
                                <form class="d-inline"
                                      method="POST"
                                      action="{{ $user->is_active ? route('users.deactivate',$user) : route('users.activate',$user) }}">
                                    @csrf
                                    <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>

@endsection
