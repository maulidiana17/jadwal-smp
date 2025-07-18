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
        Schema::create('absensi', function (Blueprint $table) {
            $table->integer('id', true);
            $table->char('nis', 20);
            $table->string('nama_lengkap')->nullable();
            $table->string('kelas', 20)->nullable();
            $table->date('tgl_absen');
            $table->time('jam_masuk');
            $table->time('jam_keluar')->nullable();
            $table->string('foto_masuk');
            $table->string('foto_keluar')->nullable();
            $table->text('location_masuk');
            $table->text('location_keluar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
