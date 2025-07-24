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
            $table->string('kategori_2')->nullable();
            $table->foreignId('mitra_id_2')->nullable()->constrained('bidan_mitras')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn('kategori_2');
            $table->dropColumn('mitra_id_2');
        });
    }
};