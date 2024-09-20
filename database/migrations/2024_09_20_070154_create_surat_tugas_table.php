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
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->string('nama_direktur')->nullable();
            $table->string('jabatan_direktur')->nullable();
            $table->string('alamat_direktur')->nullable();
            $table->string('nama_karyawan');
            $table->string('nik_karyawan');
            $table->string('jabatan_karyawan');
            $table->string('tugas');
            $table->string('tempat');
            $table->boolean('stemple');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};
