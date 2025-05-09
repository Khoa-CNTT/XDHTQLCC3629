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
        Schema::table('lich_su_van_chuyens', function (Blueprint $table) {
            $table->string('transaction_hash')->nullable();
            $table->text('metadata_uri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_su_van_chuyens', function (Blueprint $table) {
            //
        });
    }
};
