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
        Schema::create('surat_keterangan_bekerjas', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->string('nama_manager')->nullable();
            $table->string('jabatan_manager')->nullable();
            $table->string('alamat')->nullable();
            $table->string('nama_karyawan');
            $table->string('nik_karyawan');
            $table->string('unit_karyawan');
            $table->string('department_karyawan');
            $table->string('jabatan_karyawan');
            $table->string('alamat_karyawan');
            $table->string('tgl_masuk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keterangan_bekerjas');
    }
};
