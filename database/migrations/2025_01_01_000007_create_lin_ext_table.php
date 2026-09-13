<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lin_ext', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('poradi');
            $table->unsignedInteger('kod_dopravy')->comment('From CIS JŘ MHD lookup');
            $table->string('oznaceni_linky', 10);
            $table->boolean('preference_oznaceni')->default(false);
            $table->string('rezerva', 254)->nullable();

            $table->primary(['cislo_linky', 'rozliseni_linky', 'poradi']);
            $table->foreign(['cislo_linky', 'rozliseni_linky'])
                ->references(['cislo_linky', 'rozliseni_linky'])->on('linky');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lin_ext');
    }
};
