<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toilet_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->time('jam');
            $table->string('lokasi')->index();
            $table->string('lokasi_detail')->nullable();
            $table->string('petugas')->nullable();
            $table->string('status')->default('bersih')->index();
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->text('tanda_tangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lokasi', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toilet_inspections');
    }
};
