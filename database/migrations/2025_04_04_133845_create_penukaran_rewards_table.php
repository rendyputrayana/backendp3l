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
        Schema::create('penukaran_rewards', function (Blueprint $table) {
            $table->id('id_penukaran');
            $table->date('tanggal_penukaran')->nullable();
            $table->unsignedBigInteger('id_pembeli');
            $table->unsignedBigInteger('id_merchandise');
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembelis')->onDelete('cascade');
            $table->foreign('id_merchandise')->references('id_merchandise')->on('merchandises')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penukaran_rewards');
    }
};
