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
        Schema::table('nha_san_xuats', function (Blueprint $table) {
            $table->double('vi_do')->nullable();
            $table->double('kinh_do')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nha_san_xuats', function (Blueprint $table) {
            //
        });
    }
};
