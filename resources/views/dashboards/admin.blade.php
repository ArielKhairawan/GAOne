@php
// LOGIKA REAL-TIME: Otomatis mengubah angka jadi format (+1 / -1 / 0) beserta warnanya
if (!function_exists('formatTrend')) {
    function formatTrend($val) {
        $val = (int)$val;
        if ($val > 0) return ['text' => '↑ +' . $val, 'color' => 'success'];
        if ($val < 0) return ['text' => '↓ ' . $val, 'color' => 'danger'];
        return ['text' => '− 0', 'color' => 'neutral'];
    }
}

$kpis = [
    ['title' => 'Total Pengguna', 'trend' => formatTrend($data['trend_pengguna'] ?? 0), 'val' => $data['total_user'], 'sub' => 'Pengguna aktif sistem'],
    ['title' => 'Kendaraan Aktif', 'trend' => formatTrend($data['trend_kendaraan'] ?? 0), 'val' => $data['total_kendaraan'], 'sub' => 'Pemantauan operasional'],
    ['title' => 'Pengeluaran BBM', 'trend' => formatTrend($data['trend_bbm'] ?? 0), 'val' => 'Rp ' . number_format($data['total_pengeluaran_bbm'], 0, ',', '.'), 'sub' => 'Akumulasi bulan ini'],
    ['title' => 'Inspeksi WC', 'trend' => formatTrend($data['trend_wc'] ?? 0), 'val' => $data['total_inspeksi_wc'], 'sub' => 'Aktivitas 7 hari terakhir'],
    ['title' => 'Permintaan ATK', 'trend' => formatTrend($data['trend_atk'] ?? 0), 'val' => $data['total_permintaan_atk'], 'sub' => 'Permintaan tercatat'],
    ['title' => 'Pemesanan Ruangan', 'trend' => formatTrend($data['trend_meeting'] ?? 0), 'val' => $data['total_booking_meeting'], 'sub' => 'Ruangan terpakai'],
    ['title' => 'Permintaan Konsumsi', 'trend' => formatTrend($data['trend_konsumsi'] ?? 0), 'val' => $data['total_permintaan_konsumsi'], 'sub' => 'Pesanan aktif'],
    ['title' => 'Total Notifikasi', 'trend' => formatTrend($data['trend_notifikasi'] ?? 0), 'val' => $data['total_notifikasi'], 'sub' => 'Aktivitas terbaru'],
];
@endphp

<div class="row g-4 mb-4">
    @foreach($kpis as $kpi)
    <div class="col-xl-3">
        <div class="metric-card" style="padding: 20px; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div style="font-size: 13px; font-weight: 600; color: #475569;">{{ $kpi['title'] }}</div>

                @if($kpi['trend']['color'] == 'success')
                    <div style="background: rgba(16,185,129,.15); color: #10B981; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $kpi['trend']['text'] }}</div>
                @elseif($kpi['trend']['color'] == 'danger')
                    <div style="background: rgba(225,29,72,.15); color: #E11D48; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $kpi['trend']['text'] }}</div>
                @else
                    <div style="background: var(--surface-3); color: var(--text-3); padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $kpi['trend']['text'] }}</div>
                @endif
            </div>
            <div>
                <div style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px; line-height: 1.2;">{{ $kpi['val'] }}</div>
                <div style="font-size: 12px; color: #64748B;">{{ $kpi['sub'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">

    <div class="col-xl-8">
        <div class="metric-card mb-4" style="padding: 24px; border-radius: 16px; background: #ffffff; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div style="font-weight: 700; font-size: 16px; color: #0F172A;">Pengeluaran BBM Bulanan</div>
                <div class="d-flex align-items-center gap-2">
                
                </div>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="chartFuelMonthly"></canvas>
            </div>
        </div>

        <div class="metric-card" style="padding: 24px; border-radius: 16px; background: #ffffff; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div style="font-weight: 700; font-size: 16px; color: #0F172A;">Aktivitas Inspeksi WC (7 Hari)</div>
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center gap-2"><span style="width: 12px; height: 3px; background: #10b981;"></span><span style="font-size: 12px; color: #64748B; font-weight: 500;">Inspeksi</span></div>
                    <div class="d-flex align-items-center gap-2"><span style="width: 12px; height: 3px; background: #e11d48;"></span><span style="font-size: 12px; color: #64748B; font-weight: 500;">Kotor</span></div>
                </div>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="chartToiletActivity"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">

        <div class="mb-4">
            @include('dashboards.partials.notifications', ['notifications' => $data['notifications'] ?? []])
        </div>

        <div class="metric-card" style="padding: 24px; border-radius: 16px; background: #ffffff; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="font-weight: 700; font-size: 16px; color: #0F172A; margin-bottom: 24px;">Kendaraan Berdasarkan Status</div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="chartVehicleStatus"></canvas>
            </div>
        </div>

    </div>
</div>
