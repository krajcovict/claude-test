<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verze_jdf', function (Blueprint $table) {
            $table->id();
            $table->string('cislo_verze_jdf', 4)->default('1.10');
            $table->unsignedSmallInteger('cislo_du')->nullable()->comment('Číslo DÚ (dopravního úřadu), max 3 digits');
            $table->string('okres_kraj', 2)->nullable();
            $table->string('identifikace_davky', 20)->nullable();
            $table->date('datum_vyroby_davky');
            $table->string('jmeno', 60)->nullable();
            $table->timestamp('imported_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verze_jdf');
    }
};
