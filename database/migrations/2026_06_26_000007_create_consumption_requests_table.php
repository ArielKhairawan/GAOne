<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumption_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_booking_id')->nullable()->constrained('meeting_bookings')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('tanggal')->index();
            $table->string('departemen')->nullable();
            $table->string('nama_acara');
            $table->unsignedInteger('jumlah_peserta')->default(1);
            $table->json('jenis_konsumsi');
            $table->text('detail_konsumsi')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_requests');
    }
};
