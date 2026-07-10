<?php

namespace App\Services\Dashboard;

use App\Models\AtkItem;
use App\Models\AtkRequest;
use App\Models\Complaint;
use App\Models\ConsumptionRequest;
use App\Models\FuelLog;
use App\Models\MeetingBooking;
use App\Models\SystemNotification;
use App\Models\ToiletInspection;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\Monitoring\FuelLogService;
use App\Services\Monitoring\ToiletInspectionService;
use App\Services\Monitoring\VehicleService;

class RoleDashboardService
{
    /**
     * Urutan prioritas role untuk menentukan dashboard mana yang ditampilkan
     * jika seorang user memiliki lebih dari satu role.
     */
    private const ROLE_PRIORITY = ['Admin', 'Manager', 'Finance', 'GA Staff', 'Driver', 'Petugas Kebersihan', 'Karyawan'];

    public function __construct(
        private FuelLogService $fuelLogs,
        private VehicleService $vehicles,
        private ToiletInspectionService $toiletInspections,
    ) {
    }

    public function primaryRoleFor(User $user): string
    {
        foreach (self::ROLE_PRIORITY as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return 'Karyawan';
    }

    public function dataFor(User $user, string $role): array
    {
        return match ($role) {
            'Admin' => $this->forAdmin(),
            'Manager' => $this->forManager(),
            'Finance' => $this->forFinance(),
            'GA Staff' => $this->forGaStaff(),
            'Driver' => $this->forDriver($user),
            'Petugas Kebersihan' => $this->forPetugasKebersihan($user),
            default => $this->forKaryawan($user),
        };
    }

    private function recentNotifications(?int $userId = null, int $limit = 5)
    {
        return SystemNotification::query()
            ->when($userId, fn ($q) => $q->where(fn ($sub) => $sub->where('user_id', $userId)->orWhereNull('user_id')))
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function forAdmin(): array
    {
        return [
            'total_user' => User::count(),
            'total_kendaraan' => Vehicle::count(),
            'total_pengeluaran_bbm' => FuelLog::sum('total_harga'),
            'total_inspeksi_wc' => ToiletInspection::count(),
            'total_permintaan_atk' => AtkRequest::count(),
            'total_booking_meeting' => MeetingBooking::count(),
            'total_permintaan_konsumsi' => ConsumptionRequest::count(),
            'total_notifikasi' => SystemNotification::count(),
            'notifications' => $this->recentNotifications(),
        ];
    }

    private function forManager(): array
    {
        return [
            'pengeluaran_bbm_bulan_ini' => $this->fuelLogs->dashboardStats()['total_pengeluaran_bulan_ini'],
            'kendaraan_aktif' => Vehicle::where('status', 'aktif')->count(),
            'kendaraan_servis' => Vehicle::where('status', 'servis')->count(),
            'wc_status' => $this->toiletInspections->dashboardStats(),
            'atk_menunggu' => AtkRequest::where('status', 'submitted')->count(),
            'meeting_menunggu' => MeetingBooking::where('status', 'submitted')->count(),
            'atk_pending_list' => AtkRequest::with('requester')->where('status', 'submitted')->latest()->limit(5)->get(),
            'meeting_pending_list' => MeetingBooking::with(['requester', 'room'])->where('status', 'submitted')->latest()->limit(5)->get(),
            'notifications' => $this->recentNotifications(),
        ];
    }

    private function forFinance(): array
    {
        $fuelTotal = FuelLog::sum('total_harga');
        $atkMovementOutValue = 0; // ATK tidak memiliki harga satuan di skema saat ini (lihat REFACTOR_NOTES.md).
        $consumptionCount = ConsumptionRequest::whereIn('status', ['approved', 'selesai'])->count();

        return [
            'pengeluaran_bbm' => $fuelTotal,
            'pengeluaran_atk_note' => $atkMovementOutValue,
            'permintaan_konsumsi_selesai' => $consumptionCount,
            'rekap' => [
                'bbm' => $fuelTotal,
                'po' => \App\Models\PurchaseOrder::whereIn('status', ['approved', 'ordered', 'completed'])->sum('total_amount'),
                'travel' => \App\Models\TravelRequest::where('status', 'approved')->sum('estimated_cost'),
            ],
            'notifications' => $this->recentNotifications(),
        ];
    }

    private function forGaStaff(): array
    {
        $today = today();

        return [
            'aktivitas_hari_ini' => [
                'fuel' => FuelLog::whereDate('tanggal_pengisian', $today)->count(),
                'toilet' => ToiletInspection::whereDate('tanggal', $today)->count(),
                'atk' => AtkRequest::whereDate('created_at', $today)->count(),
                'meeting' => MeetingBooking::whereDate('created_at', $today)->count(),
            ],
            'kendaraan_servis' => Vehicle::where('status', 'servis')->get(),
            'fuel_terbaru' => FuelLog::with('vehicle')->latest('tanggal_pengisian')->limit(5)->get(),
            'toilet_terbaru' => ToiletInspection::latest('tanggal')->limit(5)->get(),
            'atk_terbaru' => AtkRequest::with('requester')->latest()->limit(5)->get(),
            'meeting_terbaru' => MeetingBooking::with(['requester', 'room'])->latest()->limit(5)->get(),
            'notifications' => $this->recentNotifications(),
        ];
    }

    private function forDriver(User $user): array
    {
        return [
            'kendaraan_saya' => Vehicle::where('driver_id', $user->id)->get(),
            'riwayat_bbm' => FuelLog::with('vehicle')->ownedBy($user->id)->latest('tanggal_pengisian')->limit(10)->get(),
            'notifications' => $this->recentNotifications($user->id),
        ];
    }

    private function forPetugasKebersihan(User $user): array
    {
        return [
            'today' => $this->toiletInspections->dashboardStats(),
            'riwayat_saya' => ToiletInspection::where('petugas_id', $user->id)->latest('tanggal')->limit(10)->get(),
            'belum_diperiksa' => collect(config('monitoring.toilet_locations'))->filter(function (string $lokasi) {
                return ! ToiletInspection::where('lokasi', $lokasi)->whereDate('tanggal', today())->exists();
            })->values(),
            'temuan_terbaru' => ToiletInspection::whereIn('status', ['kurang_bersih', 'kotor'])->latest('tanggal')->limit(5)->get(),
            'notifications' => $this->recentNotifications($user->id),
        ];
    }

    private function forKaryawan(User $user): array
    {
        return [
            'pengaduan_saya' => Complaint::where('user_id', $user->id)->latest()->limit(5)->get(),
            'atk_saya' => AtkRequest::where('user_id', $user->id)->latest()->limit(5)->get(),
            'booking_saya' => MeetingBooking::with('room')->where('user_id', $user->id)->latest()->limit(5)->get(),
            'konsumsi_saya' => ConsumptionRequest::where('user_id', $user->id)->latest()->limit(5)->get(),
            'notifications' => $this->recentNotifications($user->id),
        ];
    }
}
