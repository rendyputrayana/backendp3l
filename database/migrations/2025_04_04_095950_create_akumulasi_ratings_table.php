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
        Schema::create('akumulasi_ratings', function (Blueprint $table) {
            $table->id('id_akumulasi');
            $table->decimal('akumulasi',2,1)->default(0);
            $table->unsignedBigInteger('id_penitip')->nullable();
            $table->foreign('id_penitip')->references('id_penitip')->on('penitips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akumulasi_ratings');
    }
};
