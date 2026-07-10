<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Toilet Checklist Items
    |--------------------------------------------------------------------------
    | Daftar item checklist kebersihan WC. Tambahkan / hapus item di sini saja,
    | TIDAK PERLU mengubah migration atau struktur tabel apa pun.
    | Setiap kali inspeksi baru dibuat, baris toilet_inspection_items akan
    | dibuat otomatis berdasarkan daftar ini.
    */
    'toilet_checklist_items' => [
        'Bau',
        'Lantai',
        'Dinding',
        'Tempat Sampah',
        'Sabun',
        'Tisu',
        'Cermin',
        'Wastafel',
        'Kloset',
        'Saluran Air',
        'Bak Penampung Air',
        'Lampu',
        'Exhaust Fan',
        'Pintu',
        'Ventilasi',
    ],

    'toilet_checklist_item_statuses' => [
        'baik' => 'Baik',
        'kurang' => 'Kurang',
        'rusak' => 'Rusak / Kotor',
    ],

    'toilet_locations' => [
        'WC Pria',
        'WC Wanita',
        'WC Umum',
        'WC Musholla',
        'Lokasi Lainnya',
    ],

    'toilet_statuses' => [
        'bersih' => 'Bersih',
        'kurang_bersih' => 'Kurang Bersih',
        'kotor' => 'Kotor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vehicle Options
    |--------------------------------------------------------------------------
    */
    'vehicle_statuses' => [
        'aktif' => 'Aktif',
        'servis' => 'Servis',
        'tidak_aktif' => 'Tidak Aktif',
    ],

    'vehicle_types' => [
        'Mobil Operasional',
        'Truk',
        'Pickup',
        'Bus',
        'Motor',
        'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fuel Options
    |--------------------------------------------------------------------------
    */
    'fuel_types' => [
        'Pertalite',
        'Pertamax',
        'Pertamax Turbo',
        'Solar',
        'Dexlite',
        'Pertamina Dex',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Thresholds
    |--------------------------------------------------------------------------
    | Nilai default ini dapat disesuaikan tanpa mengubah kode, baik langsung
    | di sini maupun melalui environment variable di .env.
    */
    'fuel_budget_threshold_monthly' => env('MONITORING_FUEL_BUDGET_THRESHOLD', 50000000),

    'fuel_consumption_min_kmpl' => env('MONITORING_FUEL_MIN_KMPL', 8),

    'toilet_inspection_interval_hours' => env('MONITORING_TOILET_INTERVAL_HOURS', 6),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Workflow Status Labels
    |--------------------------------------------------------------------------
    | Status mentah ini didorong oleh App\Services\ApprovalEngine (lihat
    | app/Services/ApprovalEngine.php) yang dipakai bersama oleh modul ATK,
    | Booking Meeting, Permintaan Konsumsi, dan Pengaduan. "selesai" adalah
    | status tambahan khusus aplikasi yang diset manual setelah approved.
    */
    'workflow_status_labels' => [
        'draft' => 'Draft',
        'submitted' => 'Menunggu',
        'diproses' => 'Diproses',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'revision' => 'Revisi',
        'selesai' => 'Selesai',
    ],

    /*
    |--------------------------------------------------------------------------
    | ATK Inventory
    |--------------------------------------------------------------------------
    */
    'atk_item_statuses' => [
        'tersedia' => 'Tersedia',
        'stok_menipis' => 'Stok Menipis',
        'habis' => 'Habis',
    ],

    'atk_units' => ['pcs', 'box', 'pack', 'rim', 'lusin', 'unit', 'botol'],

    'atk_movement_types' => [
        'masuk' => 'Barang Masuk',
        'keluar' => 'Barang Keluar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Meeting Room
    |--------------------------------------------------------------------------
    */
    'meeting_room_statuses' => [
        'tersedia' => 'Tersedia',
        'digunakan' => 'Digunakan',
        'maintenance' => 'Maintenance',
        'tidak_aktif' => 'Tidak Aktif',
    ],

    'meeting_room_facilities' => [
        'Proyektor', 'Smart TV', 'Whiteboard', 'Speaker', 'Mikrofon',
        'Video Conference', 'AC', 'WiFi', 'Stop Kontak', 'Printer',
        'Podium', 'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | Consumption Request
    |--------------------------------------------------------------------------
    */
    'consumption_types' => [
        'Makan Siang', 'Snack Basah', 'Snack Kering', 'Coffee Break',
        'Air Mineral', 'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | CSAT
    |--------------------------------------------------------------------------
    */
    'csat_rating_labels' => [
        1 => 'Sangat Tidak Puas',
        2 => 'Tidak Puas',
        3 => 'Cukup',
        4 => 'Puas',
        5 => 'Sangat Puas',
    ],

    'csat_low_rating_threshold' => 2,

    'csat_modules' => [
        'meeting' => 'Booking Ruang Meeting',
        'consumption' => 'Permintaan Konsumsi',
        'atk' => 'Permintaan ATK',
        'complaint' => 'Pengaduan',
    ],

    /*
    |--------------------------------------------------------------------------
    | Complaint (Pengaduan)
    |--------------------------------------------------------------------------
    */
    'complaint_statuses' => [
        'menunggu' => 'Menunggu',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
    ],

];
