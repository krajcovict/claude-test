<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linky', function (Blueprint $table) {
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->string('nazev_linky', 254);
            $table->string('ic_dopravce', 10);
            $table->unsignedInteger('rozliseni_dopravce');
            $table->char('typ_linky', 1)->comment('A,B,N,P,V,Z,D');
            $table->char('dopravni_prostredek', 1)->comment('A=Autobus,E=Tramvaj,L=Lanová dráha,M=Metro,P=Přívoz,T=Trolejbus');
            $table->boolean('objizdkovy_jr')->default(false);
            $table->boolean('seskupeni_spoju')->default(false);
            $table->boolean('pouziti_oznacniku')->default(false);
            $table->string('rezerva', 5)->nullable();
            $table->string('cislo_licence', 48)->nullable();
            $table->date('platnost_lic_od')->nullable();
            $table->date('platnost_lic_do')->nullable();
            $table->date('platnost_jr_od')->nullable();
            $table->date('platnost_jr_do')->nullable();

            $table->primary(['cislo_linky', 'rozliseni_linky']);
            $table->foreign(['ic_dopravce', 'rozliseni_dopravce'])
                ->references(['ic', 'rozliseni_dopravce'])->on('dopravci');
        });

        DB::statement("ALTER TABLE linky ADD CONSTRAINT chk_linky_typ CHECK (typ_linky IN ('A','B','N','P','V','Z','D'))");
        DB::statement("ALTER TABLE linky ADD CONSTRAINT chk_linky_prostredek CHECK (dopravni_prostredek IN ('A','E','L','M','P','T'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('linky');
    }
};
