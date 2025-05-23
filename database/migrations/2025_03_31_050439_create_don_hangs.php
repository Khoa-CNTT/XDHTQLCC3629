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
        Schema::create('don_hangs', function (Blueprint $table) {
            $table->id();
            $table->uuid('ma_don_hang')->unique();
            $table->integer('user_id')->nullable();
            $table->integer('id_nguoi_duyet')->nullable();
            $table->timestamp('ngay_dat')->useCurrent();
            $table->date('ngay_giao')->nullable();
            $table->integer('tong_tien')->nullable();
            $table->integer('tinh_trang')->default(0)->nullable();// 0: Chờ xác nhận, 1: Đang chuẩn bị hàng, 2: Đang vận chuyển, 3: Hoàn thành, 4: Hủy
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_hangs');
    }
};
