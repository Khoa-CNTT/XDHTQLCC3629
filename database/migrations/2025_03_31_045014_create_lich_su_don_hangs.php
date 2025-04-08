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
            $table->integer('tinh_trang')->nullable()->default(0);
            //Đại lý: 0: Chờ xác nhận, 1: Đã xác nhận, 2: Đang chuẩn bị hàng, 5: Đang giao hàng, 3: Done, 4: Đã hủy
            //Nhà sản xuất: 0: Chờ xác nhận, 1: Đang chuẩn bị hàng, 2: Chuẩn bị hàng xong (giao cho đv vc), 5: Đang giao hàng, 4: Đã hủy, 3: Done
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
