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
            $table->integer('id_kho_hang')->nullable();
            $table->timestamp('thoi_gian_den')->nullable();
            $table->timestamp('thoi_gian_di')->nullable();
            $table->integer('thu_tu')->nullable(); //thứ tự
            $table->text('mo_ta')->nullable();
            $table->integer('tinh_trang')->nullable()->default(0);
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
