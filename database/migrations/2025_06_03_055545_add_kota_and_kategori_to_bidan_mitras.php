<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('bidan_mitras', function (Blueprint $table) {
            $table->string('kota')->nullable();
            $table->string('kategori')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('bidan_mitras', function (Blueprint $table) {
            $table->dropColumn('kota');
            $table->dropColumn('kategori');
        });
    }
};
