@extends('layouts.app')

@section('title', ($role->exists ? 'Edit' : 'Tambah') . ' Role')
@section('page-title', ($role->exists ? 'Edit' : 'Tambah') . ' Role')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Manajemen &middot; Role</span>
        <h1 class="section-title">{{ $role->exists ? 'Edit ' . $role->name : 'Tambah Role Baru' }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('admin.roles.index') }}">Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <strong>Periksa kembali isian Anda:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
    @csrf
    @if($role->exists) @method('PUT') @endif

    <div class="card mb-4">
        <div class="card-body p-4">
            <label class="form-label">Nama Role <span style="color:var(--crimson)">*</span></label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $role->name) }}" style="max-width:400px">
        </div>
    </div>

    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border)">
            <div style="font-size:14px; font-weight:600; color:var(--text)">Permission</div>
            <p class="small text-muted mb-0">Pilih hak akses yang dimiliki role ini, dikelompokkan per modul.</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @foreach($permissions as $module => $modulePermissions)
                <div class="col-md-4">
                    <div style="font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px; text-transform:capitalize">{{ $module }}</div>
                    @foreach($modulePermissions as $permission)
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" name="permissions[]" id="perm-{{ $permission->id }}"
                               value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', $assigned)))>
                        <label class="form-check-label" for="perm-{{ $permission->id }}" style="font-size:13px">{{ $permission->name }}</label>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-5">Simpan</button>
        <a class="btn btn-outline-secondary px-4" href="{{ route('admin.roles.index') }}">Batal</a>
    </div>
</form>

@endsection
