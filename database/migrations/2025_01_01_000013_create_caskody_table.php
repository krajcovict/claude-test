<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caskody', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_spoje');
            $table->unsignedInteger('cislo_casoveho_kodu');
            $table->string('oznaceni_casoveho_kodu', 2);
            $table->string('typ_casove_kodu', 1)->nullable()
                ->comment('1..8, see JDF appendix table *1b), or NULL for info-only codes');
            $table->date('datum_od')->nullable();
            $table->date('datum_do')->nullable();
            $table->string('poznamka', 254)->nullable();

            $table->primary(['cislo_linky', 'rozliseni_linky', 'cislo_spoje', 'cislo_casoveho_kodu']);
            $table->foreign(['cislo_linky', 'rozliseni_linky', 'cislo_spoje'])
                ->references(['cislo_linky', 'rozliseni_linky', 'cislo_spoje'])->on('spoje');
            $table->index(['datum_od', 'datum_do']);
        });

        DB::statement("ALTER TABLE caskody ADD CONSTRAINT chk_caskody_typ CHECK (typ_casove_kodu IS NULL OR typ_casove_kodu IN ('1','2','3','4','5','6','7','8'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('caskody');
    }
};
