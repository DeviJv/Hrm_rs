<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_mulai');
            $table->date('tgl_akhir');
            $table->string('nama_pelatihan');
            $table->string('narasumber');
            $table->decimal('jumlah_jam', 5, 2);
            $table->string('foto')->nullable();
            $table->string('undangan')->nullable();
            $table->string('materi')->nullable();
            $table->string('absensi')->nullable();
            $table->string('keterangan')->nullable();
            $table->json('peserta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('pelatihans');
    }
};