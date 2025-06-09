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
        Schema::create('penitips', function (Blueprint $table) {
            $table->id('id_penitip');
            $table->string('nama_penitip');
            $table->string('no_ktp');
            $table->string('no_telepon');
            $table->string('alamat_penitip');
            $table->string('foto_ktp');
            $table->bigInteger('saldo')->default(0);
            $table->unsignedBigInteger('poin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penitips');
    }
};
