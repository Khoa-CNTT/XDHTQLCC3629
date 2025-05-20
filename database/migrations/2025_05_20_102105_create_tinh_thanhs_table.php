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
        Schema::create('tinh_thanhs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_tinh_thanh')->nullable();
            $table->double('vi_do')->nullable();
            $table->double('kinh_do')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tinh_thanhs');
    }
};
