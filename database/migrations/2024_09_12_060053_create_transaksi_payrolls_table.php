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
        Schema::create('transaksi_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->string('tunjangan')->nullable();
            $table->string('gaji_pokok')->nullable();
            $table->string('makan_transport')->nullable();
            $table->string('insentif')->nullable();
            $table->string('bpjs kesehatan')->nullable();
            $table->string('ketenagakerjaan')->nullable();
            $table->string('pajak')->nullable();
            $table->string('tidak_masuk')->nullable();
            $table->string('piutang')->nullable();
            $table->string('lembur')->nullable();
            $table->string('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_payrolls');
    }
};