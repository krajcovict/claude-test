<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dopravci', function (Blueprint $table) {
            $table->string('ic', 10);
            $table->unsignedInteger('rozliseni_dopravce');
            $table->string('dic', 14)->nullable();
            $table->string('obchodni_jmeno', 254);
            $table->unsignedTinyInteger('druh_firmy')->comment('1 = právnická osoba, 2 = fyzická osoba');
            $table->string('jmeno_fyz_osoby', 254)->nullable();
            $table->string('sidlo', 254);
            $table->string('telefon_sidla', 48);
            $table->string('telefon_dispecink', 48)->nullable();
            $table->string('telefon_informace', 48)->nullable();
            $table->string('fax', 48)->nullable();
            $table->string('email', 48)->nullable();
            $table->string('www', 48)->nullable();

            $table->primary(['ic', 'rozliseni_dopravce']);
        });

        DB::statement('ALTER TABLE dopravci ADD CONSTRAINT chk_dopravci_druh_firmy CHECK (druh_firmy IN (1,2))');
    }

    public function down(): void
    {
        Schema::dropIfExists('dopravci');
    }
};
