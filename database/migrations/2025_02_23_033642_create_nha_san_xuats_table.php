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
        Schema::create('nha_san_xuats', function (Blueprint $table) {
            $table->id();
            $table->string('ten_cong_ty')->nullable();
            $table->string('loai_doi_tac')->nullable();
            $table->string('dia_chi')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('email')->nullable();
            $table->date('ngay_tao')->nullable();
            $table->date('ngay_cap_nhat')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->decimal('so_du_tai_khoan',15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nha_san_xuats');
    }
};
