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
        Schema::create('san_pham_n_s_x_e_s', function (Blueprint $table) {
            $table->id();
            $table->integer('id_san_pham')->nullable();
            $table->integer('id_nha_san_xuat')->nullable();
            $table->string('ma_lo_hang')->nullable();
            $table->date('ngay_san_xuat')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_pham_n_s_x_e_s');
    }
};
