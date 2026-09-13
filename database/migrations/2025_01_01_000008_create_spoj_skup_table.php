<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spoj_skup', function (Blueprint $table) {
            $table->unsignedInteger('kod_skupiny_spoju')->primary();
            $table->unsignedInteger('poradi');
            $table->string('nazev', 48);
            $table->string('popis', 254)->nullable();
            $table->string('rezerva', 254)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spoj_skup');
    }
};
