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
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->string('huy_bo_boi')->nullable();
        });

        Schema::table('lich_su_don_hangs', function (Blueprint $table) {
            $table->text('huy_bo_boi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->string('huy_bo_boi')->nullable();
        });

        Schema::table('lich_su_don_hangs', function (Blueprint $table) {
            $table->text('huy_bo_boi')->nullable();
        });
    }
};
