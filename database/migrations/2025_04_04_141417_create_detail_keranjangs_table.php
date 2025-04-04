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
        Schema::create('detail_keranjangs', function (Blueprint $table) {
            $table->id('id_keranjang');
            $table->unsignedBigInteger('kode_produk');
            $table->unsignedBigInteger('id_pembeli');
            $table->foreign('kode_produk')->references('kode_produk')->on('barangs')->onDelete('cascade');
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembelis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_keranjangs');
    }
};
