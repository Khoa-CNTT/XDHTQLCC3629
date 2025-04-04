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
        Schema::create('don_vi_van_chuyens', function (Blueprint $table) {
            $table->id();
            $table->string('ten_cong_ty')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('dia_chi')->nullable();
            $table->decimal('cuoc_van_chuyen', 15, 3)->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->string('loai_tai_khoan')->default('Đơn vị vận chuyển');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_vi_van_chuyens');
    }
};
