<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zaslinky', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_tarifni')->comment('Sequence position of the stop within the line');
            $table->string('tarifni_pasmo', 50)->nullable();
            $table->unsignedInteger('cislo_zastavky');
            $table->string('prumerna_doba', 5)->nullable()->comment('Minutes from the first stop of the line');
            foreach (range(1, 3) as $n) {
                $table->string("pev_kod_{$n}", 5)->nullable();
            }

            $table->primary(['cislo_linky', 'rozliseni_linky', 'cislo_tarifni']);
            $table->foreign(['cislo_linky', 'rozliseni_linky'])
                ->references(['cislo_linky', 'rozliseni_linky'])->on('linky');
            $table->foreign('cislo_zastavky')->references('cislo_zastavky')->on('zastavky');
            foreach (range(1, 3) as $n) {
                $table->foreign("pev_kod_{$n}")->references('cislo_pevneho_kodu')->on('pevnykod');
            }
            $table->index('cislo_zastavky');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zaslinky');
    }
};
