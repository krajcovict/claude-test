<?php

namespace Database\Factories;

use App\Models\SpojSkupina;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpojSkupinaFactory extends Factory
{
    protected $model = SpojSkupina::class;

    public function definition(): array
    {
        return [
            'kod_skupiny_spoju' => $this->faker->unique()->numberBetween(1, 999),
            'poradi' => $this->faker->numberBetween(1, 10),
            'nazev' => $this->faker->randomElement(['Pracovné dni', 'Soboty', 'Nedele a sviatky', 'Školské prázdniny']),
            'popis' => null,
            'rezerva' => null,
        ];
    }
}
