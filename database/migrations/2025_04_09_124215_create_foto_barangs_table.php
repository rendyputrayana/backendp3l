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
        Schema::create('foto_barangs', function (Blueprint $table) {
            $table->id('id_foto');
            $table->unsignedBigInteger('kode_produk');
            $table->string('foto_barang');
            $table->foreign('kode_produk')->references('kode_produk')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_barangs');
    }
};
