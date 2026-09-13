<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navaznosti', function (Blueprint $table) {
            $table->id();
            $table->char('typ_navaznosti', 1)->comment('m = spoj vyčká, M = navazuje spoj');
            $table->unsignedInteger('cislo_linky');
            $table->unsignedInteger('rozliseni_linky');
            $table->unsignedInteger('cislo_spoje');
            $table->unsignedInteger('cislo_tarifni');
            $table->unsignedInteger('cislo_pr_linky');
            $table->unsignedInteger('cislo_zastavky_pr_linky');
            $table->unsignedInteger('kod_oznacniku_pr_linky')->nullable();
            $table->unsignedInteger('cislo_vych_konc_zast_pr');
            $table->unsignedInteger('kod_vych_konc_oznac_pr')->nullable();
            $table->unsignedInteger('doba_cekani')->comment('Minutes');

            $table->foreign(['cislo_linky', 'rozliseni_linky', 'cislo_tarifni'])
                ->references(['cislo_linky', 'rozliseni_linky', 'cislo_tarifni'])->on('zaslinky');
            $table->foreign(['cislo_linky', 'rozliseni_linky', 'cislo_spoje'])
                ->references(['cislo_linky', 'rozliseni_linky', 'cislo_spoje'])->on('spoje');
            $table->index(['cislo_pr_linky', 'cislo_zastavky_pr_linky']);
        });

        DB::statement("ALTER TABLE navaznosti ADD CONSTRAINT chk_navaznosti_typ CHECK (typ_navaznosti IN ('m','M'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('navaznosti');
    }
};
