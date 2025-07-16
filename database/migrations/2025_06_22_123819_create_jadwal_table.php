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
        Schema::create('jadwal', function (Blueprint $table) {
        $table->id();
        $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
        $table->foreignId('mapel_id')->constrained('mapel')->onDelete('cascade');
        $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
        $table->foreignId('ruangan_id')->nullable()->constrained('ruangan')->onDelete('cascade');
        $table->foreignId('waktu_id')->constrained('waktu')->onDelete('cascade');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
