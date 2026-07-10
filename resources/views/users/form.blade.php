@extends('layouts.app')

@section('title', $user->exists ? 'Edit User' : 'Tambah User')
@section('page-title', $user->exists ? 'Edit User' : 'Tambah User')
@section('page-subtitle', 'Kelola informasi pengguna dan hak akses sistem')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">System · Users</span>
        <h1 class="section-title">{{ $user->exists ? 'Edit ' . $user->name : 'Tambah Pengguna Baru' }}</h1>
        <p class="section-subtitle">{{ $user->exists ? 'Perbarui data dan hak akses pengguna.' : 'Isi data pengguna baru yang akan mengakses sistem.' }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <strong>Periksa kembali isian Anda:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="post"
      action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($user->exists)
        @method('PUT')
    @endif

    {{-- Informasi Pengguna --}}
    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px;color:var(--sky)">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            <div style="font-size:14px; font-weight:600; color:var(--text)">Informasi Pengguna</div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span style="color:var(--crimson)">*</span></label>
                    <input class="form-control" name="name"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Nama lengkap">
                    @error('name')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email <span style="color:var(--crimson)">*</span></label>
                    <input class="form-control" name="email" type="email"
                           value="{{ old('email', $user->email) }}"
                           placeholder="nama@perusahaan.com">
                    @error('email')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nomor HP</label>
                    <input class="form-control" name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="08xx-xxxx-xxxx">
                    @error('phone')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Departemen <span style="color:var(--crimson)">*</span></label>
                    <input class="form-control" name="department"
                           value="{{ old('department', $user->department) }}"
                           placeholder="General Affairs">
                    @error('department')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jabatan <span style="color:var(--crimson)">*</span></label>
                    <input class="form-control" name="position"
                           value="{{ old('position', $user->position) }}"
                           placeholder="Staff GA">
                    @error('position')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Password {{ $user->exists ? '' : '*' }}
                        @if($user->exists)
                            <span style="font-size:11px; color:var(--text-4); font-weight:400">(kosongkan jika tidak diubah)</span>
                        @endif
                    </label>
                    <input class="form-control" name="password" type="password"
                           placeholder="{{ $user->exists ? 'Isi untuk mengubah' : 'min. 8 karakter' }}"
                           {{ $user->exists ? '' : 'required' }}>
                    @error('password')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>
    </div>

    {{-- Hak Akses --}}
    <div class="card mb-4">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px;color:var(--amber)">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
            </svg>
            <div style="font-size:14px; font-weight:600; color:var(--text)">Hak Akses & Status</div>
        </div>
        <div class="card-body p-4">

            <div class="mb-4">
                <label class="form-label">Role Pengguna</label>
                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:6px">
                    @foreach($roles as $role)
                    <label style="display:flex; align-items:center; gap:8px; padding:8px 14px; border:1.5px solid var(--border); border-radius:8px; cursor:pointer; font-size:13.5px; font-weight:500; color:var(--text-2); transition:all .15s"
                           onmouseover="this.style.borderColor='var(--sky)'" onmouseout="this.style.borderColor='var(--border)'">
                        <input type="checkbox" name="roles[]" value="{{ $role }}"
                               @checked($user->hasRole($role))
                               style="accent-color:var(--sky)">
                        {{ $role }}
                    </label>
                    @endforeach
                </div>
                @error('roles')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
            </div>

            <div>
                <input type="hidden" name="is_active" value="0">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $user->is_active ?? true))
                           style="width:16px;height:16px;accent-color:var(--emerald)">
                    <span style="font-size:14px; font-weight:500; color:var(--text)">Aktifkan akun pengguna ini</span>
                </label>
                <p style="font-size:12px; color:var(--text-4); margin-top:4px; margin-left:26px">
                    Pengguna yang tidak aktif tidak dapat masuk ke sistem.
                </p>
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
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
    </div>

</form>

@endsection
