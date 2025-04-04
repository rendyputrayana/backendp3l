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
        Schema::create('hunters', function (Blueprint $table) {
            $table->id('id_hunter');
            $table->string('nama_hunter');
            $table->string('password_hunter');
            $table->bigInteger('saldo')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hunters');
    }
};
