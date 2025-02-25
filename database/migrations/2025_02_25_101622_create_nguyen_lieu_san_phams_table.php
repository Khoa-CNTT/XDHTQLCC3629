<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nguyen_lieu_san_phams', function (Blueprint $table) {
            $table->id();
            $table->string('ma_san_pham')->nullable();
            $table->integer('id_nguyen_lieu')->nullable();
            $table->integer('so_luong_nguyen_lieu')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguyen_lieu_san_phams');
    }
};
