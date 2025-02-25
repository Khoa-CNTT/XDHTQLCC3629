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
        Schema::create('chi_tiet_san_phams', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don_hang')->nullable();
            $table->string('ma_san_pham')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->decimal('don_gia')->nullable();
            $table->integer('so_luong')->nullable();
            $table->string('don_vi_tinh')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_san_phams');
    }
};
