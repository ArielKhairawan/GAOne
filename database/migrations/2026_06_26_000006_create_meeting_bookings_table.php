<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_room_id')->constrained('meeting_rooms')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('tanggal')->index();
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->string('departemen')->nullable();
            $table->string('nama_kegiatan');
            $table->unsignedInteger('jumlah_peserta')->default(1);
            $table->text('catatan')->nullable();
            $table->boolean('butuh_konsumsi')->default(false);
            $table->string('status')->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['meeting_room_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_bookings');
    }
};
