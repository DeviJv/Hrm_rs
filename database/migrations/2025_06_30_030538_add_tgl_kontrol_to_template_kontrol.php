<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('template_kontrols', function (Blueprint $table) {
            $table->date('tgl_kontrol')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('template_kontrols', function (Blueprint $table) {
            $table->dropColumn('tgl_kontol');
        });
    }
};