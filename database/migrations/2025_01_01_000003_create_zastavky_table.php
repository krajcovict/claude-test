<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zastavky', function (Blueprint $table) {
            $table->unsignedInteger('cislo_zastavky')->primary();
            $table->string('nazev_obce', 48);
            $table->string('cast_obce', 48)->nullable();
            $table->string('blizsi_misto', 48)->nullable();
            $table->string('blizka_obec', 3)->nullable()->comment('Required when stat = CZ or SK');
            $table->string('stat', 3);
            foreach (range(1, 6) as $n) {
                $table->string("pev_kod_{$n}", 5)->nullable();
            }

            foreach (range(1, 6) as $n) {
                $table->foreign("pev_kod_{$n}")
                    ->references('cislo_pevneho_kodu')->on('pevnykod');
            }
            $table->index(['nazev_obce', 'cast_obce']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zastavky');
    }
};
