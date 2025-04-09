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
        Schema::create('kho_trung_chuyens', function (Blueprint $table) {
            $table->id();
            $table->string('ten_kho')->nullable();
            $table->string('tinh_thanh')->nullable();
            $table->string('dia_chi')->nullable();
            $table->double('vi_do')->nullable();
            $table->double('kinh_do')->nullable();
            $table->integer('loai_kho')->nullable()->default(0);
            $table->text('mo_ta')->nullable();
            $table->text('tinh_trang')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kho_trung_chuyens', function (Blueprint $table) {
            //
        });
    }
};
