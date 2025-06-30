<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('template_kontrols', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm');
            $table->string('status')->nullable();
            $table->string('jk')->nullable();
            $table->string('umur')->nullable();
            $table->longText('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('diagnosa')->nullable();
            $table->string('tindakan')->nullable();
            $table->timestamp('hpl')->nullable();
            $table->string('penjamin')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('template_kontrols');
    }
};