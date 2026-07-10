<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
            public function up(): void
            {
            Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plat_nomor')->unique();
            $table->string('jenis_kendaraan');
            $table->string('merk')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->string('driver')->nullable();
            $table->string('status')->default('aktif')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
