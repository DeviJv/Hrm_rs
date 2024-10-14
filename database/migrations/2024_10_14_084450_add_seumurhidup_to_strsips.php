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
        Schema::table('strsips', function (Blueprint $table) {
            $table->boolean('seumur_hidup')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('strsips', function (Blueprint $table) {
            $table->dropColumn('seumur_hidup');
        });
    }
};