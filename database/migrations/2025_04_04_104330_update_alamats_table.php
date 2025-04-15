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
        Schema::table('alamats', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pembeli');
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembelis')->onDelete('cascade');
            $table->boolean('is_default')->default(false); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alamats', function (Blueprint $table) {
            //
        });
    }
};
