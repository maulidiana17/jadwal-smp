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
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->integer('id', true);
            $table->char('nis', 20)->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('kelas', 20)->nullable();
            $table->date('tanggal_izin')->nullable();
            $table->date('tanggal_izin_akhir')->nullable();
            $table->char('status', 1)->nullable()->comment('i=izin s=sakit');
            $table->string('keterangan')->nullable();
            $table->string('file_surat')->nullable();
            $table->char('status_approved', 1)->nullable()->default('0')->comment('0:pending 1:disetujui 2:ditolak 3:revisi');
            $table->text('catatan_penolakan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
