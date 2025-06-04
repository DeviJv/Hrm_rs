<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->string('no_tlp')->nullable();
            $table->string('kelas')->nullable();
            $table->string('pasien_rujukan')->nullable();
            $table->string('jenis')->nullable();
            $table->string('status')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('jaminan')->nullable();
            $table->string('usia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn('no_tlp');
            $table->dropColumn('kelas');
            $table->dropColumn('pasien_rujukan');
            $table->dropColumn('jenis');
            $table->dropColumn('status');
            $table->dropColumn('keterangan');
            $table->dropColumn('jaminan');
            $table->dropColumn('usia');
        });
    }
};