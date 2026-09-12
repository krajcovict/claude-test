<?php

namespace Database\Factories;

use App\Models\AltLinka;
use Illuminate\Database\Eloquent\Factories\Factory;

class AltLinkaFactory extends Factory
{
    protected $model = AltLinka::class;

    public function definition(): array
    {
        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'alt_cislo_linky' => (string) $this->faker->numberBetween(100, 999),
            'stat' => $this->faker->randomElement(['CZ', 'AT', 'HU']),
        ];
    }
}
