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
        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->string('nama_karyawan')->nullable()->change();
            $table->string('nik_karyawan')->nullable()->change();
            $table->string('jabatan_karyawan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->string('nama_karyawan')->nullable()->change();
            $table->string('nik_karyawan')->nullable()->change();
            $table->string('jabatan_karyawan')->nullable()->change();
        });
    }
};
