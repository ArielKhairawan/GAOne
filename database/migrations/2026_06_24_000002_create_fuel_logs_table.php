<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->restrictOnDelete();
            $table->date('tanggal_pengisian')->index();
            $table->string('driver')->nullable();
            $table->string('jenis_bahan_bakar');
            $table->decimal('harga_per_liter', 15, 2);
            $table->decimal('jumlah_liter', 10, 2);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->unsignedInteger('kilometer_awal');
            $table->unsignedInteger('kilometer_akhir');
            $table->unsignedInteger('jarak_tempuh')->default(0);
            $table->decimal('konsumsi_bbm', 8, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jenis_bahan_bakar']);
            $table->index(['vehicle_id', 'tanggal_pengisian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
