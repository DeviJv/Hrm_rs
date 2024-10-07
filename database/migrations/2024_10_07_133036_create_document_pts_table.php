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
        Schema::create('document_pts', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('type_document');
            $table->string('unit')->nullable();
            $table->string('upload_document')->nullable();
            $table->string('masa_berlaku_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_pts');
    }
};
