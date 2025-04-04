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
        Schema::create('lich_su_van_chuyens', function (Blueprint $table) {
            $table->id();
            $table->integer('id_don_hang')->nullable();
            $table->integer('id_don_vi_van_chuyen')->nullable();
            $table->string('dia_diem_hien_tai')->nullable();
            $table->timestamp('thoi_gian_cap_nhat')->nullable()->useCurrent();
            $table->tinyInteger('tinh_trang')->nullable()->default(0); // 0: Đã nhận, 1: Đang vận chuyển, 2: Đã giao hàng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_van_chuyens');
    }
};
