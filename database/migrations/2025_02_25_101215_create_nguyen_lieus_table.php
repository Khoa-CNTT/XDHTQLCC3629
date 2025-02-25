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
        Schema::create('nguyen_lieus', function (Blueprint $table) {
            $table->id();
            $table->string('ma_nguyen_lieu')->nullable();
            $table->string('ten_nguyen_lieu')->nullable();
            $table->string('ma_lo_hang')->nullable();
            $table->string('ma_nha_cung_cap')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguyen_lieus');
    }
};
