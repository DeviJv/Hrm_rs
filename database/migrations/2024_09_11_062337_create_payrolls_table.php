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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->date('periode');
            $table->string('gaji_pokok');
            $table->string('transport')->nullable();
            $table->string('makan')->nullable();
            $table->string('penyesuaian')->nullable();
            $table->string('insentif')->nullable();
            $table->string('fungsional')->nullable();
            $table->string('fungsional_it')->nullable();
            $table->string('sub_total')->nullable();
            $table->json('tidak_masuk')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};