<?php

namespace App\Providers;

use App\Models\AtkItem;
use App\Models\AtkRequest;
use App\Models\Complaint;
use App\Models\ConsumptionRequest;
use App\Models\FuelLog;
use App\Models\MeetingBooking;
use App\Models\ToiletInspection;
use App\Models\Vehicle;
use App\Policies\AtkItemPolicy;
use App\Policies\AtkRequestPolicy;
use App\Policies\ComplaintPolicy;
use App\Policies\ConsumptionRequestPolicy;
use App\Policies\FuelLogPolicy;
use App\Policies\MeetingBookingPolicy;
use App\Policies\ToiletInspectionPolicy;
use App\Policies\VehiclePolicy;
use App\Repositories\Contracts\AtkItemRepositoryInterface;
use App\Repositories\Contracts\FuelLogRepositoryInterface;
use App\Repositories\Contracts\ToiletInspectionRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Eloquent\AtkItemRepository;
use App\Repositories\Eloquent\FuelLogRepository;
use App\Repositories\Eloquent\ToiletInspectionRepository;
use App\Repositories\Eloquent\VehicleRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Repository binding. Untuk modul yang lebih sederhana (Meeting,
     * Consumption, Complaint) Repository Pattern tidak diterapkan secara
     * terpisah karena logika query-nya sudah cukup sederhana untuk
     * ditangani langsung oleh Service + Eloquent (lihat REFACTOR_NOTES.md).
     */
    public function register(): void
    {
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(FuelLogRepositoryInterface::class, FuelLogRepository::class);
        $this->app->bind(ToiletInspectionRepositoryInterface::class, ToiletInspectionRepository::class);
        $this->app->bind(AtkItemRepositoryInterface::class, AtkItemRepository::class);
    }

    public function boot(): void
    {
        // Project ini punya CSS kustom (.pagination, .page-link) bergaya Bootstrap,
        // namun view paginasi default Laravel (Tailwind) tidak cocok dengan markup
        // tersebut sehingga tombol paginasi tampil tanpa style. Bootstrap-5 adalah
        // view bawaan Laravel yang markupnya cocok dengan CSS yang sudah ada.
        Paginator::useBootstrapFive();

        // Policy didaftarkan eksplisit (selain otomatis terdeteksi lewat
        // konvensi penamaan App\Models\X -> App\Policies\XPolicy) agar
        // hubungan antar modul terlihat jelas dan tidak bergantung pada
        // auto-discovery saja.
        Gate::policy(Vehicle::class, VehiclePolicy::class);
        Gate::policy(FuelLog::class, FuelLogPolicy::class);
        Gate::policy(ToiletInspection::class, ToiletInspectionPolicy::class);
        Gate::policy(AtkItem::class, AtkItemPolicy::class);
        Gate::policy(AtkRequest::class, AtkRequestPolicy::class);
        Gate::policy(MeetingBooking::class, MeetingBookingPolicy::class);
        Gate::policy(ConsumptionRequest::class, ConsumptionRequestPolicy::class);
        Gate::policy(Complaint::class, ComplaintPolicy::class);
    }
}
