<?php

namespace Database\Factories;

use App\Models\ZasLinka;
use App\Models\Zastavka;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZasLinkaFactory extends Factory
{
    protected $model = ZasLinka::class;

    public function definition(): array
    {
        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_tarifni' => $this->faker->unique()->numberBetween(1, 30),
            'tarifni_pasmo' => (string) $this->faker->numberBetween(1, 5), // real data used plain small numbers, e.g. "1"
            'cislo_zastavky' => Zastavka::factory(),
            'prumerna_doba' => $this->faker->optional(0.2)->numerify('##'),
            'pev_kod_1' => $this->faker->optional(0.15)->randomElement(['29']),
            'pev_kod_2' => null,
            'pev_kod_3' => null,
        ];
    }
}
