@can('atk.view')
    <div class="sidebar-group">Inventaris</div>

    <a href="{{ route('atk.items.index') }}" class="{{ request()->routeIs('atk.items.*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-2.18c.07-.44.18-.88.18-1.36C18 2.06 15.96 0 13.36 0 11.76 0 10.3.8 9.34 2.04L8 3.67 6.66 2.04A4.611 4.611 0 003.64 0C1.04 0-.96 2.06-.96 4.64c0 .48.11.92.18 1.36H-2v14h22V6zm-4 12H8V8h8v10z"/></svg>
        Data ATK
    </a>

    @can('atk.edit')
    <a href="{{ route('atk.stock-in.index') }}" class="{{ request()->routeIs('atk.stock-in.*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Barang Masuk
    </a>

    <a href="{{ route('atk.stock-out.index') }}" class="{{ request()->routeIs('atk.stock-out.*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13H5v-2h14v2z"/></svg>
        Barang Keluar
    </a>
    @endcan

    <a href="{{ route('atk.requests.index') }}" class="{{ request()->routeIs('atk.requests.*') ? 'active' : '' }}">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Permintaan ATK
    </a>
@endcan
