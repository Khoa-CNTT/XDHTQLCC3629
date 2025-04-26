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
            $table->integer('id_nha_san_xuat')->nullable();
            $table->integer('id_nha_cung_cap')->nullable();
            $table->string('ma_nguyen_lieu')->nullable();
            $table->string('ten_nguyen_lieu')->nullable();
            $table->integer('so_luong')->nullable();
            $table->string('don_vi_tinh')->nullable();
            $table->date('ngay_san_xuat')->nullable();
            $table->date('han_su_dung')->nullable();
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
