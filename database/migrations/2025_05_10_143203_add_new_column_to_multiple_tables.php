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
        Schema::table('dai_lies', function (Blueprint $table) {
            $table->string('dia_chi_vi')->nullable();
        });

        Schema::table('nha_san_xuats', function (Blueprint $table) {
            $table->string('dia_chi_vi')->nullable();
        });

        Schema::table('don_vi_van_chuyens', function (Blueprint $table) {
            $table->string('dia_chi_vi')->nullable();
        });

        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->string('dia_chi_vi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_tables', function (Blueprint $table) {
            //
        });
    }
};
