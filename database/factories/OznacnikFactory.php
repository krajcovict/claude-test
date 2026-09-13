<?php

namespace Database\Factories;

use App\Models\Oznacnik;
use App\Models\Zastavka;
use Illuminate\Database\Eloquent\Factories\Factory;

class OznacnikFactory extends Factory
{
    protected $model = Oznacnik::class;

    public function definition(): array
    {
        return [
            'cislo_zastavky' => Zastavka::factory(),
            'kod_oznacniku' => $this->faker->numberBetween(1, 9),
            'nazev' => $this->faker->optional(0.4)->streetName(),
            'smer_popis' => $this->faker->optional(0.4)->randomElement(['smer centrum', 'smer Trnava', 'smer Hlohovec']),
            'stanoviste' => $this->faker->optional(0.3)->bothify('Nástupište #'),
            'rezerva1' => null,
            'rezerva2' => null,
        ];
    }
}
