<?php

namespace Database\Seeders;

use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\AtkCategory;
use App\Models\AtkItem;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\MeetingRoom;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Role final yang dipakai sistem ini. Role lain (mis. dari iterasi
     * sebelumnya seperti "Super Admin", "Operator BBM", dst) akan dihapus
     * di seedRoles() agar tidak ada role tambahan yang tidak diinginkan.
     */
    private const FINAL_ROLES = ['Admin', 'Manager', 'Finance', 'GA Staff', 'Driver', 'Petugas Kebersihan', 'Karyawan'];

    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seedPermissions();
        $this->seedRoles();
        $users = $this->seedDefaultUsers();
        $this->seedApprovalWorkflows($users['admin']);
        $this->seedFacilities();
        $this->seedAtkItems();
        $this->seedVehicles($users);
        $this->seedMeetingRooms();
    }

    private function seedPermissions(): void
    {
        // 'meeting', 'consumption', dan 'complaint' ditambahkan untuk modul
        // baru (Booking Ruang Meeting, Permintaan Konsumsi, Pengaduan).
        // Pola cross-product modul x ability ini konsisten dengan modul yang
        // sudah ada di atas.
        $modules = [
            'user', 'approval', 'travel', 'facility', 'atk', 'po', 'csat', 'notification', 'report', 'audit',
            'fuel', 'vehicle', 'toilet', 'meeting', 'consumption', 'complaint',
        ];
        $abilities = ['view', 'create', 'edit', 'delete', 'approve', 'export'];

        foreach ($modules as $module) {
            foreach ($abilities as $ability) {
                Permission::findOrCreate("$module.$ability");
            }
        }
    }

    private function seedRoles(): void
    {
        // Bersihkan role dari iterasi sebelumnya yang sudah tidak dipakai,
        // supaya hanya FINAL_ROLES yang tersisa di sistem (sesuai instruksi:
        // "Gunakan hanya role berikut... Jangan membuat role tambahan").
        Role::whereNotIn('name', self::FINAL_ROLES)->get()->each(function (Role $role) {
            $role->delete();
        });

        $roles = [
            'Admin' => Permission::all()->pluck('name')->all(),

            'Manager' => [
                'approval.view', 'approval.approve',
                'fuel.view', 'vehicle.view', 'toilet.view',
                'atk.view', 'atk.approve',
                'meeting.view', 'meeting.approve',
                'consumption.view', 'consumption.approve',
                'travel.view', 'travel.approve',
                'facility.view', 'facility.approve',
                'po.view', 'csat.view',
                'report.view', 'report.export',
                'notification.view',
            ],

            'Finance' => [
                'approval.view', 'approval.approve',
                'fuel.view', 'fuel.export',
                'atk.view', 'consumption.view',
                'po.view', 'po.create', 'po.edit', 'po.approve',
                'travel.view', 'travel.approve',
                'report.view', 'report.export',
                'notification.view',
            ],

            'GA Staff' => [
                'approval.view', 'approval.approve',
                'fuel.view', 'fuel.create', 'fuel.edit', 'fuel.delete', 'fuel.export',
                'vehicle.view', 'vehicle.create', 'vehicle.edit', 'vehicle.delete',
                'toilet.view', 'toilet.export',
                'atk.view', 'atk.create', 'atk.edit', 'atk.delete', 'atk.export', 'atk.approve',
                'meeting.view', 'meeting.create', 'meeting.edit', 'meeting.approve',
                'consumption.view', 'consumption.create', 'consumption.edit', 'consumption.approve',
                'complaint.view', 'complaint.edit',
                'travel.view', 'travel.approve',
                'facility.view', 'facility.create', 'facility.edit', 'facility.approve',
                'po.view', 'po.approve', 'csat.view',
                'report.view', 'report.export',
                'notification.view',
            ],

            'Driver' => [
                'vehicle.view',
                'fuel.view', 'fuel.create',
                'notification.view',
            ],

            'Petugas Kebersihan' => [
                'toilet.view', 'toilet.create', 'toilet.edit', 'toilet.export',
                'notification.view',
            ],

            'Karyawan' => [
                'travel.view', 'travel.create',
                'facility.view', 'facility.create',
                'atk.view', 'atk.create',
                'meeting.view', 'meeting.create',
                'consumption.view', 'consumption.create',
                'complaint.view', 'complaint.create',
                'csat.view', 'csat.create',
                'notification.view',
            ],
        ];

        foreach ($roles as $name => $permissions) {
            Role::findOrCreate($name)->syncPermissions($permissions);
        }
    }

    /**
     * Lima default user wajib (sesuai instruksi) ditambah dua user demo
     * untuk role Driver & Petugas Kebersihan, supaya seluruh dashboard
     * role-based dapat dicoba langsung. Password semua user: "password".
     */
    private function seedDefaultUsers(): array
    {
        $defaults = [
            'admin' => ['name' => 'Administrator', 'email' => 'admin@gaone.test', 'role' => 'Admin', 'department' => 'Management'],
            'finance' => ['name' => 'Finance Officer', 'email' => 'finance@gaone.test', 'role' => 'Finance', 'department' => 'Finance'],
            'manager' => ['name' => 'Operations Manager', 'email' => 'manager@gaone.test', 'role' => 'Manager', 'department' => 'Management'],
            'staff' => ['name' => 'GA Staff', 'email' => 'staff@gaone.test', 'role' => 'GA Staff', 'department' => 'General Affairs'],
            'employee' => ['name' => 'Employee', 'email' => 'employee@gaone.test', 'role' => 'Karyawan', 'department' => 'Operations'],
            'driver' => ['name' => 'Driver Demo', 'email' => 'driver@gaone.test', 'role' => 'Driver', 'department' => 'General Affairs'],
            'petugas' => ['name' => 'Petugas Kebersihan Demo', 'email' => 'kebersihan@gaone.test', 'role' => 'Petugas Kebersihan', 'department' => 'General Affairs'],
        ];

        $users = [];
        foreach ($defaults as $key => $cfg) {
            $user = User::firstOrCreate(
                ['email' => $cfg['email']],
                [
                    'name' => $cfg['name'],
                    'department' => $cfg['department'],
                    'position' => $cfg['role'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $user->syncRoles([$cfg['role']]);
            $users[$key] = $user;
        }

        // Akun legacy dari iterasi refactor sebelumnya tetap dipertahankan
        // (bukan data produksi nyata, jadi aman) supaya tidak ada yang
        // mendadak tidak bisa login memakai kredensial lama.
        $legacyAdmin = User::firstOrCreate(
            ['email' => 'admin@ga1.local'],
            [
                'name' => 'GA1 Administrator',
                'phone' => '080000000001',
                'department' => 'General Affairs',
                'position' => 'System Administrator',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );
        $legacyAdmin->syncRoles(['Admin']);

        return $users;
    }

    private function seedApprovalWorkflows(User $admin): void
    {
        // Urutan step = urutan approval SETELAH permintaan disubmit oleh
        // pemohon (mis. Karyawan). Karyawan tidak masuk sebagai step karena
        // dia adalah pengaju, bukan approver.
        $workflows = [
            'travel' => ['GA Staff', 'Manager', 'Finance'],
            'facility' => ['GA Staff', 'Manager'],
            'atk' => ['GA Staff', 'Manager'],
            'po' => ['GA Staff', 'Finance'],
            'meeting' => ['GA Staff', 'Manager'],
            'consumption' => ['GA Staff', 'Manager'],
        ];

        foreach ($workflows as $module => $steps) {
            $workflow = ApprovalWorkflow::firstOrCreate(
                ['module' => $module, 'name' => ucfirst($module).' Default Approval'],
                ['is_active' => true, 'created_by' => $admin->id]
            );

            foreach ($steps as $sequence => $role) {
                // updateOrCreate (bukan firstOrCreate) supaya perbaikan urutan
                // step pada seeder ini juga ikut diterapkan walau project
                // sudah pernah di-seed sebelumnya dengan urutan yang lama.
                ApprovalStep::updateOrCreate(
                    ['approval_workflow_id' => $workflow->id, 'sequence' => $sequence + 1],
                    ['role_name' => $role, 'is_required' => true]
                );
            }
        }
    }

    private function seedFacilities(): void
    {
        $facilities = ['Aula'];

        foreach ($facilities as $name) {
            $category = FacilityCategory::firstOrCreate(['name' => $name]);

            Facility::firstOrCreate(
                ['code' => str($name)->slug('-')->upper()->toString()],
                [
                    'facility_category_id' => $category->id,
                    'name' => $name.' Utama',
                    'capacity' => 10,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedAtkItems(): void
    {
        $items = [
            ['category' => 'Stationery', 'code' => 'ATK-001', 'name' => 'Pulpen', 'satuan' => 'pcs', 'stock' => 50, 'minimum_stock' => 20, 'lokasi_penyimpanan' => 'Gudang A - Rak 1'],
            ['category' => 'Stationery', 'code' => 'ATK-002', 'name' => 'Kertas A4', 'satuan' => 'rim', 'stock' => 8, 'minimum_stock' => 10, 'lokasi_penyimpanan' => 'Gudang A - Rak 2'],
            ['category' => 'Printing', 'code' => 'ATK-003', 'name' => 'Tinta Printer', 'satuan' => 'botol', 'stock' => 0, 'minimum_stock' => 5, 'lokasi_penyimpanan' => 'Gudang B - Rak 1'],
            ['category' => 'Office Supplies', 'code' => 'ATK-004', 'name' => 'Stapler', 'satuan' => 'pcs', 'stock' => 25, 'minimum_stock' => 10, 'lokasi_penyimpanan' => 'Gudang A - Rak 3'],
        ];

        foreach ($items as $row) {
            $category = AtkCategory::firstOrCreate(['name' => $row['category']]);

            AtkItem::firstOrCreate(
                ['code' => $row['code']],
                [
                    'atk_category_id' => $category->id,
                    'name' => $row['name'],
                    'satuan' => $row['satuan'],
                    'stock' => $row['stock'],
                    'minimum_stock' => $row['minimum_stock'],
                    'lokasi_penyimpanan' => $row['lokasi_penyimpanan'],
                ]
            );
        }
    }

    private function seedVehicles(array $users): void
    {
        $driverId = $users['driver']->id ?? null;

        $vehicles = [
            ['plat_nomor' => 'B 1234 ABC', 'jenis_kendaraan' => 'Mobil Operasional', 'merk' => 'Toyota Avanza', 'tahun' => 2021, 'driver' => 'Budi Santoso', 'driver_id' => $driverId, 'status' => 'aktif'],
            ['plat_nomor' => 'B 5678 DEF', 'jenis_kendaraan' => 'Truk', 'merk' => 'Mitsubishi Colt Diesel', 'tahun' => 2019, 'driver' => 'Agus Wijaya', 'status' => 'aktif'],
            ['plat_nomor' => 'B 9012 GHI', 'jenis_kendaraan' => 'Pickup', 'merk' => 'Daihatsu Gran Max', 'tahun' => 2020, 'driver' => 'Eko Prasetyo', 'status' => 'servis'],
            ['plat_nomor' => 'B 3456 JKL', 'jenis_kendaraan' => 'Motor', 'merk' => 'Honda Supra X', 'tahun' => 2022, 'driver' => 'Dedi Kurniawan', 'status' => 'aktif'],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(['plat_nomor' => $vehicle['plat_nomor']], $vehicle);
        }
    }

    private function seedMeetingRooms(): void
    {
        $rooms = [
            ['kode_ruangan' => 'MR-01', 'nama_ruangan' => 'Ruang Garuda', 'lokasi' => 'Lantai 2', 'kapasitas' => 12, 'fasilitas' => ['Proyektor', 'Whiteboard', 'AC', 'WiFi'], 'status' => 'tersedia'],
            ['kode_ruangan' => 'MR-02', 'nama_ruangan' => 'Ruang Elang', 'lokasi' => 'Lantai 2', 'kapasitas' => 6, 'fasilitas' => ['Smart TV', 'Whiteboard', 'AC', 'WiFi'], 'status' => 'tersedia'],
            ['kode_ruangan' => 'MR-03', 'nama_ruangan' => 'Ruang Rajawali', 'lokasi' => 'Lantai 3', 'kapasitas' => 30, 'fasilitas' => ['Proyektor', 'Video Conference', 'Speaker', 'Mikrofon', 'Podium', 'AC', 'WiFi'], 'status' => 'tersedia'],
        ];

        foreach ($rooms as $room) {
            MeetingRoom::firstOrCreate(['kode_ruangan' => $room['kode_ruangan']], $room);
        }
    }
}
