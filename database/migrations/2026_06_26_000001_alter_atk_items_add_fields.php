<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atk_items', function (Blueprint $table) {
            $table->string('satuan')->default('pcs')->after('name');
            $table->string('lokasi_penyimpanan')->nullable()->after('minimum_stock');
        });
    }

    public function down(): void
    {
        Schema::table('atk_items', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'lokasi_penyimpanan']);
        });
    }
};
