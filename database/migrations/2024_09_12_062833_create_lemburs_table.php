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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->date('tgl_lembur');
            $table->time('jm_mulai');
            $table->time('jm_selesai');
            $table->string('jumlah_jam')->nullable();
            $table->string('harga_lembur')->nullable();
            $table->string('total_lembur')->nullable();
            $table->string('harga_perjam')->nullable();
            $table->string('harga_jam_pertama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};
