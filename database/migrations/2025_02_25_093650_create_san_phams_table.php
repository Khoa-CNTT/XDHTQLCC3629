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
        Schema::create('san_phams', function (Blueprint $table) {
            $table->id();
            $table->string('ma_san_pham')->nullable();
            $table->string('ten_san_pham')->nullable();
            $table->text('mo_ta')->nullable();
            $table->integer('id_danh_muc')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->text('hinh_anh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_phams');
    }
};
