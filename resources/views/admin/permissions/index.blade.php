@extends('layouts.app')

@section('title', 'Manajemen Permission')
@section('page-title', 'Manajemen Permission')

@section('content')

<div class="mb-5">
    <span class="section-eyebrow">Manajemen</span>
    <h1 class="section-title">Permission</h1>
    <p class="section-subtitle">Daftar seluruh permission yang tersedia di sistem, dikelompokkan per modul. Untuk mengatur kepemilikan permission per role, buka menu Role.</p>
</div>

<div class="row g-4">
    @foreach($grouped as $module => $permissions)
    <div class="col-md-4">
        <div class="card">
            <div style="padding:14px 18px; border-bottom:1px solid var(--border); text-transform:capitalize; font-weight:600; font-size:13px">{{ $module }}</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        @foreach($permissions as $p)
                        <tr>
                            <td style="font-size:13px">{{ $p->name }}</td>
                            <td class="text-end" style="font-size:12px; color:var(--text-3)">{{ $p->roles_count }} role</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection
