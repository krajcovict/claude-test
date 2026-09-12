<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('udaje', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_udaje')->comment('Line number of the note text');
            $table->string('text', 254);

            $table->primary(['cislo_linky', 'rozliseni_linky', 'cislo_udaje']);
            $table->foreign(['cislo_linky', 'rozliseni_linky'])
                ->references(['cislo_linky', 'rozliseni_linky'])->on('linky');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('udaje');
    }
};
