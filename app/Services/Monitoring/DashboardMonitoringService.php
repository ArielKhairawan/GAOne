<?php

namespace App\Services\Monitoring;

class DashboardMonitoringService
{
    public function __construct(
        private FuelLogService $fuelLogs,
        private VehicleService $vehicles,
        private ToiletInspectionService $toiletInspections,
    ) {
    }

    public function stats(): array
    {
        return [
            'fuel' => $this->fuelLogs->dashboardStats(),
            'vehicle' => $this->vehicles->dashboardStats(),
            'toilet' => $this->toiletInspections->dashboardStats(),
        ];
    }

    public function charts(): array
    {
        return [
            'fuel_monthly_spending' => $this->fuelLogs->monthlySpendingChart(6),
            'toilet_activity' => $this->toiletInspections->activityChart(),
            'vehicle_by_status' => $this->vehicles->dashboardStats()['by_status'],
        ];
    }
}
