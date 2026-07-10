{{-- Modul GA dari versi sebelumnya. Tidak dihapus (data & fitur tetap utuh
     dan dapat diakses), namun dikelompokkan terpisah di bawah karena sidebar
     final yang diminta difokuskan pada 12 modul utama. --}}
@canany(['travel.view', 'po.view', 'facility.view', 'csat.view'])
    <div class="sidebar-group">Modul Lama (GA)</div>

    @can('travel.view')
    <a href="{{ route('modules.index', 'travel-requests') }}" class="{{ request()->is('modules/travel-requests*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M21 3L3 10.53v.98l6.84 2.65L12.48 21h.98L21 3z"/></svg>
        Business Travel
    </a>
    @endcan

    @can('facility.view')
    <a href="{{ route('modules.index', 'facility-bookings') }}" class="{{ request()->is('modules/facility-bookings*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
        Booking Fasilitas (Aula, dll)
    </a>
    @endcan

    @can('po.view')
    <a href="{{ route('modules.index', 'vendors') }}" class="{{ request()->is('modules/vendors*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4v2l8 5 8-5V4zm0 4.236l-8 5-8-5V20h16V8.236z"/></svg>
        Vendor
    </a>

    <a href="{{ route('modules.index', 'purchase-orders') }}" class="{{ request()->is('modules/purchase-orders*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Purchase Order
    </a>
    @endcan

    @can('csat.view')
    <a href="{{ route('modules.index', 'surveys') }}" class="{{ request()->is('modules/surveys*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2z"/></svg>
        Semua Hasil Survei (Admin)
    </a>
    @endcan
@endcanany
