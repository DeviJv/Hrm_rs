<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidan_mitra_id')->constrained('bidan_mitras')->onDelete('cascade');
            $table->foreignId('tindakan_id')->nullable()->constrained('tindakans');
            $table->string('nama');
            $table->string('operasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('pasiens');
    }
};