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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->string('jk')->nullable();
            $table->string('agama')->nullable();
            $table->string('nakes')->nullable();
            $table->string('department')->nullable();
            $table->string('jabatan')->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->string('tgl_lahir')->nullable();
            $table->string('status')->nullable()->default('kontrak');
            $table->string('nik_ktp')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('universitas')->nullable();
            $table->string('no_ijazah')->nullable();
            $table->string('str')->nullable();
            $table->string('masa_berlaku')->nullable();
            $table->string('sip')->nullable();
            $table->string('no_tlp')->nullable();
            $table->string('email')->nullable();
            $table->longText('alamat')->nullable();
            $table->boolean('aktif')->default(false);
            $table->string('bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nip')->nullable();
            $table->string('no_sk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};