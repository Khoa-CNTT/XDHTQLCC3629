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
        Schema::create('lich_su_don_hangs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('id_don_hang')->nullable();
            $table->integer('id_san_pham')->nullable();
            $table->decimal('don_gia', 15, 3)->nullable();
            $table->integer('so_luong')->nullable();
            $table->integer('tinh_trang')->nullable()->default(0);// 0: Chờ xác nhận, 1: Đang chuẩn bị, 2: Đã xong, 3: Đang vận chuyển, 4: Giao thành công, 5: Đã hủy
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_don_hangs');
    }
};
