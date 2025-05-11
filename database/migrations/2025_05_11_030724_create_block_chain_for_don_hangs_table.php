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
        Schema::create('block_chain_for_don_hangs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_don_hang')->nullable();
            $table->string('action')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->text('metadata_uri')->nullable();
            $table->string('token_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_chain_for_don_hangs');
    }
};
