<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spoje', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_spoje')
                ->comment('Odd = outbound direction, even = return direction');
            foreach (range(1, 10) as $n) {
                $table->string("pev_kod_{$n}", 5)->nullable();
            }
            $table->unsignedInteger('kod_skupiny_spoju')->nullable();

            $table->primary(['cislo_linky', 'rozliseni_linky', 'cislo_spoje']);
            $table->foreign(['cislo_linky', 'rozliseni_linky'])
                ->references(['cislo_linky', 'rozliseni_linky'])->on('linky');
            $table->foreign('kod_skupiny_spoju')->references('kod_skupiny_spoju')->on('spoj_skup');
            foreach (range(1, 10) as $n) {
                $table->foreign("pev_kod_{$n}")->references('cislo_pevneho_kodu')->on('pevnykod');
            }
            $table->index('kod_skupiny_spoju');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spoje');
    }
};
