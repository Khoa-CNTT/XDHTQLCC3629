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
        Schema::table('block_chain_for_don_hangs', function (Blueprint $table) {
            $table->integer('id_user')->nullable();
            $table->string('loai_tai_khoan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_chain_for_don_hangs', function (Blueprint $table) {
            //
        });
    }
};
