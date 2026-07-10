@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun dan keamanan login Anda')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <span class="section-eyebrow">Akun</span>
        <h1 class="section-title">Profil Pengguna</h1>
        <p class="section-subtitle">Perbarui data diri dan keamanan akun Anda.</p>
    </div>
</div>

<div class="row g-4">

    {{-- Update Profile --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px;color:var(--sky)">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <div style="font-size:14px; font-weight:600; color:var(--text)">Informasi Pribadi</div>
            </div>
            <div class="card-body p-4">

                <form method="post" enctype="multipart/form-data" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span style="color:var(--crimson)">*</span></label>
                        <input class="form-control" name="name"
                               value="{{ old('name', auth()->user()->name) }}"
                               placeholder="Nama lengkap Anda">
                        @error('name')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input class="form-control" name="phone"
                               value="{{ old('phone', auth()->user()->phone) }}"
                               placeholder="08xx-xxxx-xxxx">
                        @error('phone')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <input class="form-control" name="department"
                               value="{{ old('department', auth()->user()->department) }}"
                               placeholder="General Affairs">
                        @error('department')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Jabatan</label>
                        <input class="form-control" name="position"
                               value="{{ old('position', auth()->user()->position) }}"
                               placeholder="Staff GA">
                        @error('position')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Foto Profil</label>
                        <input class="form-control" name="photo" type="file" accept="image/*">
                        @error('photo')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <button class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                        </svg>
                        Simpan Profil
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px;color:var(--amber)">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
                <div style="font-size:14px; font-weight:600; color:var(--text)">Ubah Password</div>
            </div>
            <div class="card-body p-4">

                <form method="post" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini <span style="color:var(--crimson)">*</span></label>
                        <input class="form-control" name="current_password" type="password"
                               placeholder="Password lama Anda">
                        @error('current_password')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru <span style="color:var(--crimson)">*</span></label>
                        <input class="form-control" name="password" type="password"
                               placeholder="min. 8 karakter">
                        @error('password')<div style="font-size:12px;color:var(--crimson);margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru <span style="color:var(--crimson)">*</span></label>
                        <input class="form-control" name="password_confirmation" type="password"
                               placeholder="Ulangi password baru">
                    </div>

                    <button class="btn btn-outline-secondary">
                        <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        Ubah Password
                    </button>
                </form>

            </div>
        </div>
    </div>

</div>

@endsection
