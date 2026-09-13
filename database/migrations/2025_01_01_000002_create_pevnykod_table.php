<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pevnykod', function (Blueprint $table) {
            $table->string('cislo_pevneho_kodu', 5)->primary();
            $table->char('oznaceni_pevneho_kodu', 1)
                ->comment('The fixed-code character, e.g. X + 1..7 R # @ % | < ~ } v x ( ) $ { [ O s §');
            $table->string('rezerva', 254)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pevnykod');
    }
};
