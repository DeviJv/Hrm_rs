<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('master_fee_rujukans', function (Blueprint $table) {
            $table->id();
            $table->string('tindakan');
            $table->string('kategori');
            $table->string('umum')->default(0);
            $table->string('asuransi')->default(0);
            $table->string('bpjs')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('master_fee_rujukans');
    }
};
