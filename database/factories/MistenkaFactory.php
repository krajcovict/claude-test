<?php

namespace Database\Factories;

use App\Models\Mistenka;
use Illuminate\Database\Eloquent\Factories\Factory;

class MistenkaFactory extends Factory
{
    protected $model = Mistenka::class;

    public function definition(): array
    {
        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_spoje' => 0, // 0 = applies to every trip with a reservation option
            'text_informace' => $this->faker->sentence(6),
        ];
    }
}
