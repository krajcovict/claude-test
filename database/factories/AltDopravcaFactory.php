<?php

namespace Database\Factories;

use App\Models\AltDopravca;
use App\Models\Dopravca;
use Illuminate\Database\Eloquent\Factories\Factory;

class AltDopravcaFactory extends Factory
{
    protected $model = AltDopravca::class;

    public function definition(): array
    {
        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_spoje' => 0, // 0 = applies to every trip of the line
            'ic_dopravce' => Dopravca::factory(),
            'rozliseni_dopravce' => 0,
            'pev_kod_1' => null,
            'pev_kod_2' => null,
            'pev_kod_3' => null,
            'pev_kod_4' => null,
            'pev_kod_5' => null,
            'pev_kod_6' => null,
            'typ_casoveho_kodu' => $this->faker->optional(0.3)->randomElement(['5', '6']),
            'rezerva' => null,
            'datum_od' => $this->faker->optional(0.5)->dateTimeBetween('-3 months', 'now'),
            'datum_do' => null,
        ];
    }
}
