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
        Schema::table('penitips', function (Blueprint $table) {
            $table->unsignedBigInteger('id_akumulasi')->nullable();
            $table->foreign('id_akumulasi')->references('id_akumulasi')->on('akumulasi_ratings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penitips', function (Blueprint $table) {
            //
        });
    }
};
