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
        Schema::table('kontraks', function (Blueprint $table) {
            $table->date('tgl_akhir')->nullable()->change();
            $table->date('tgl_mulai')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->date('tgl_akhir')->nullable()->change();
            $table->date('tgl_mulai')->nullable()->change();
        });
    }
};
