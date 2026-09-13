<?php

namespace Database\Factories;

use App\Models\Udaj;
use Illuminate\Database\Eloquent\Factories\Factory;

class UdajFactory extends Factory
{
    protected $model = Udaj::class;

    public function definition(): array
    {
        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_udaje' => $this->faker->unique()->numberBetween(1, 20),
            'text' => $this->faker->sentence(8),
        ];
    }
}
