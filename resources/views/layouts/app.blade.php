<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GAOne') — Semen Andalas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-layout">

    <aside class="sidebar">

        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-logo-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 21V8l8-5 8 5v13H4zm2-2h3v-3H6v3zm0-5h3v-3H6v3zm5 5h3v-3h-3v3zm0-5h3v-3h-3v3zm5 5h3v-3h-3v3zm0-5h3v-3h-3v3zM11 3.5V7h2V3.5h-2z"/>
                    </svg>
                </div>
                <div class="sidebar-brand-text">
                    <h4>GAOne</h4>
                    <small>Semen Andalas System</small>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @include('layouts.sidebar.index')
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm" style="width:100%; justify-content: center;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>

    </aside>

    <div class="main-content">

        <header class="topbar">

            <div class="topbar-left" style="display: flex; align-items: center; gap: 12px;">
                <button class="btn-hamburger" id="btnHamburger">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                    </svg>
                </button>
                <div>
                    <div class="page-title">@yield('page-title', 'Dashboard')</div>
                    <div class="page-subtitle d-none d-md-block">@yield('page-subtitle', 'Ringkasan aktivitas sesuai role Anda')</div>
                </div>
            </div>

            <div class="topbar-right" style="display: flex; align-items: center; gap: 16px;">

                <div class="topbar-greeting d-none d-md-inline" style="font-size: 13.5px; color: var(--text-3);">
                    Halo, <span style="font-weight: 700; color: var(--text);">{{ auth()->user()->name ?? 'Admin GA' }}</span> 👋
                </div>

                <div class="topbar-waktu d-none d-md-inline-flex" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #059669; padding: 7px 14px; border-radius: 10px; font-size: 12.5px; font-weight: 600;">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/></svg>
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                </div>

                <a href="{{ route('profile.edit') }}" class="btn-profile" style="color: var(--text-2); border-color: var(--border-2); font-weight: 600;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Profil
                </a>

            </div>
        </header>

        <main class="content">
            @if(session('success') || session('status'))
                <div class="alert alert-success">{{ session('success') ?? session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any() && !request()->routeIs('login') && !request()->routeIs('register'))
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>

    </div>

</div>

@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btnHamburger');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.querySelector('.main-content');

    if(btn && sidebar && overlay && mainContent) {
        function toggleMenu() {
            // Cek ukuran layar: HP atau PC?
            if (window.innerWidth <= 768) {
                // LOGIKA HP: Munculkan menu ke tengah layar + Overlay Gelap
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                // LOGIKA PC/LAPTOP: Geser menu ke kiri & Lebarkan konten
                sidebar.classList.toggle('desktop-closed');
                mainContent.classList.toggle('desktop-expanded');
            }
        }

        // Eksekusi saat hamburger diklik
        btn.addEventListener('click', toggleMenu);

        // Eksekusi saat overlay gelap diklik (hanya berlaku di HP)
        overlay.addEventListener('click', function() {
            if (window.innerWidth <= 768) toggleMenu();
        });
    }
});
</script>

</body>
</html>
