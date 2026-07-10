@extends('layouts.app')

@section('title', 'Manajemen Role')
@section('page-title', 'Manajemen Role')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Manajemen</span>
        <h1 class="section-title">Role</h1>
        <p class="section-subtitle">Atur role dan hak akses (permission) untuk setiap role.</p>
    </div>
    @can('user.create')
    <a class="btn btn-primary" href="{{ route('admin.roles.create') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Tambah Role
    </a>
    @endcan
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Nama Role</th><th>Jumlah Permission</th><th>Jumlah User</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td class="fw-medium">{{ $role->name }}</td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-2" style="justify-content:flex-end">
                                @can('user.edit')
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.roles.edit', $role) }}">Edit</a>
                                @endcan
                                @can('user.delete')
                                <form method="post" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Hapus role ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
