<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oznacniky', function (Blueprint $table) {
            $table->unsignedInteger('cislo_zastavky');
            $table->unsignedInteger('kod_oznacniku');
            $table->string('nazev', 48)->nullable();
            $table->string('smer_popis', 48)->nullable();
            $table->string('stanoviste', 12)->nullable();
            $table->string('rezerva1', 254)->nullable();
            $table->string('rezerva2', 254)->nullable();

            $table->primary(['cislo_zastavky', 'kod_oznacniku']);
            $table->foreign('cislo_zastavky')->references('cislo_zastavky')->on('zastavky');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oznacniky');
    }
};
