<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\RoleDashboardService;
use App\Services\Monitoring\DashboardMonitoringService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(RoleDashboardService $roleDashboard, DashboardMonitoringService $monitoring)
    {
        $user = Auth::user();
        $role = $roleDashboard->primaryRoleFor($user);
        $data = $roleDashboard->dataFor($user, $role);

        $payload = [
            'user' => $user,
            'role' => $role,
            'roleView' => str($role)->slug()->toString(),
            'data' => $data,
        ];

        // Grafik Chart.js (Pengeluaran BBM bulanan, Aktivitas WC, Status
        // Kendaraan) hanya relevan untuk role yang mengawasi operasional
        // secara luas.
        if (in_array($role, ['Admin', 'Manager', 'GA Staff'], true)) {
            $payload['monitoringStats'] = $monitoring->stats();
            $payload['monitoringCharts'] = $monitoring->charts();
        }

        return view('dashboard', $payload);
    }
}
