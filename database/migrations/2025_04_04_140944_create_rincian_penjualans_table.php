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
        Schema::create('rincian_penjualans', function (Blueprint $table) {
            $table->id('id_rincian_penjualan');
            $table->unsignedBigInteger('nota_penjualan');
            $table->unsignedBigInteger('kode_produk');
            $table->foreign('nota_penjualan')->references('nota_penjualan')->on('penjualans')->onDelete('cascade');
            $table->foreign('kode_produk')->references('kode_produk')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_penjualans');
    }
};
