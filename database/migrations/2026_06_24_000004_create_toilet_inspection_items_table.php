<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toilet_inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toilet_inspection_id')->constrained('toilet_inspections')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('status')->default('baik');
            $table->timestamps();

            $table->unique(['toilet_inspection_id', 'item_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toilet_inspection_items');
    }
};
