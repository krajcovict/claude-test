<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('altdop', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_spoje')->comment('0 = applies to every trip of the line');
            $table->string('ic_dopravce', 10);
            $table->unsignedInteger('rozliseni_dopravce');
            foreach (range(1, 6) as $n) {
                $table->string("pev_kod_{$n}", 5)->nullable();
            }
            $table->string('typ_casoveho_kodu', 1)->nullable()->comment('5 = liché týdny, 6 = sudé týdny');
            $table->string('rezerva', 254)->nullable();
            $table->date('datum_od')->nullable();
            $table->date('datum_do')->nullable();

            $table->foreign(['cislo_linky', 'rozliseni_linky'])
                ->references(['cislo_linky', 'rozliseni_linky'])->on('linky');
            $table->foreign(['ic_dopravce', 'rozliseni_dopravce'])
                ->references(['ic', 'rozliseni_dopravce'])->on('dopravci');
            $table->index(['cislo_linky', 'rozliseni_linky', 'cislo_spoje']);
        });

        DB::statement("ALTER TABLE altdop ADD CONSTRAINT chk_altdop_typ CHECK (typ_casoveho_kodu IS NULL OR typ_casoveho_kodu IN ('5','6'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('altdop');
    }
};
