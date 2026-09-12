<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zasspoje', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_spoje');
            $table->unsignedInteger('cislo_tarifni');
            $table->unsignedInteger('cislo_zastavky');
            $table->unsignedInteger('kod_oznacniku')->nullable();
            $table->string('cislo_stanoviste', 48)->nullable();
            $table->string('pev_kod_1', 5)->nullable();
            $table->string('pev_kod_2', 5)->nullable();
            $table->decimal('kilometry', 7, 3)->nullable()
                ->comment('Cumulative km from the departure stop of the trip');
            $table->string('cas_prijezdu', 5)->nullable()->comment('HH:MM, or the literal "<" / "|"');
            $table->string('cas_odjezdu', 5)->nullable()->comment('HH:MM, or the literal "<" / "|"');

            $table->primary(['cislo_linky', 'rozliseni_linky', 'cislo_spoje', 'cislo_tarifni']);
            $table->foreign(['cislo_linky', 'rozliseni_linky', 'cislo_spoje'])
                ->references(['cislo_linky', 'rozliseni_linky', 'cislo_spoje'])->on('spoje');
            $table->foreign(['cislo_linky', 'rozliseni_linky', 'cislo_tarifni'])
                ->references(['cislo_linky', 'rozliseni_linky', 'cislo_tarifni'])->on('zaslinky');
            $table->foreign('cislo_zastavky')->references('cislo_zastavky')->on('zastavky');
            $table->foreign(['cislo_zastavky', 'kod_oznacniku'])
                ->references(['cislo_zastavky', 'kod_oznacniku'])->on('oznacniky');
            $table->foreign('pev_kod_1')->references('cislo_pevneho_kodu')->on('pevnykod');
            $table->foreign('pev_kod_2')->references('cislo_pevneho_kodu')->on('pevnykod');
            $table->index('cislo_zastavky');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zasspoje');
    }
};
