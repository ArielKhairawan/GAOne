<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan')->unique();
            $table->string('nama_ruangan');
            $table->string('lokasi')->nullable();
            $table->unsignedInteger('kapasitas')->default(0);
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('fasilitas')->nullable();
            $table->string('status')->default('tersedia')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_rooms');
    }
};
