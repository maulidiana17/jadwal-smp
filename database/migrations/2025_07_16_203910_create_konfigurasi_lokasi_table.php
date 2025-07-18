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
        Schema::create('konfigurasi_lokasi', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('lokasi_sekolah');
            $table->smallInteger('radius');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_lokasi');
    }
};
