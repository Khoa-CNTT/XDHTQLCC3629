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
        Schema::create('giao_dichs', function (Blueprint $table) {
            $table->id();
            $table->string('ma_giao_dich')->nullable();
            $table->string('ma_don_hang')->nullable();
            $table->string('mo_ta')->nullable();
            $table->decimal('gia_tri', 15)->nullable();
            $table->date('ngay_thuc_hien')->nullable();
            $table->string('so_tai_khoan')->nullable();
            $table->string('ma_tham_chieu')->nullable();
            $table->string('so_tai_khoan_doi_ung')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giao_dichs');
    }
};
