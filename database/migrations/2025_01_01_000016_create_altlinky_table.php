<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('altlinky', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->string('alt_cislo_linky', 20);
            $table->string('stat', 3);

            $table->primary(['cislo_linky', 'rozliseni_linky', 'alt_cislo_linky', 'stat']);
            $table->foreign(['cislo_linky', 'rozliseni_linky'])
                ->references(['cislo_linky', 'rozliseni_linky'])->on('linky');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('altlinky');
    }
};
