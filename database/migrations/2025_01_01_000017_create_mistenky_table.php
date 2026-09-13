<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mistenky', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_spoje')
                ->comment('0 = applies to every trip of the line with reservations defined');
            $table->string('text_informace', 254);

            $table->foreign(['cislo_linky', 'rozliseni_linky'])
                ->references(['cislo_linky', 'rozliseni_linky'])->on('linky');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mistenky');
    }
};
