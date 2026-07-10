@canany(['complaint.view', 'csat.view'])
    <div class="sidebar-group">Layanan</div>

    @can('complaint.view')
    <a href="{{ route('complaint.index') }}" class="{{ request()->routeIs('complaint.*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg>
        Pengaduan
    </a>
    @endcan

    @can('csat.view')
    <a href="{{ route('csat.index') }}" class="{{ request()->routeIs('csat.*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z"/></svg>
        Survei Kepuasan
    </a>
    @endcan
@endcanany
