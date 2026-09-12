<?php

namespace Database\Factories;

use App\Models\LinExt;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinExtFactory extends Factory
{
    protected $model = LinExt::class;

    public function definition(): array
    {
        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'poradi' => 1,
            'kod_dopravy' => $this->faker->numberBetween(1, 20),
            'oznaceni_linky' => (string) $this->faker->numberBetween(1, 99),
            'preference_oznaceni' => true,
            'rezerva' => null,
        ];
    }
}
