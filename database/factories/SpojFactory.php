<?php

namespace Database\Factories;

use App\Models\Spoj;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpojFactory extends Factory
{
    protected $model = Spoj::class;

    public function definition(): array
    {
        // Real Spoje.txt: pev_kod_1 was often "10" (weekday operation) or "17"+"11" together;
        // kod_skupiny_spoju was consistently "0" (meaning: no group) in this batch.
        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_spoje' => $this->faker->unique()->numberBetween(1, 400),
            'pev_kod_1' => $this->faker->optional(0.7)->randomElement(['10', '17']),
            'pev_kod_2' => $this->faker->optional(0.2)->randomElement(['11']),
            'pev_kod_3' => null,
            'pev_kod_4' => null,
            'pev_kod_5' => null,
            'pev_kod_6' => null,
            'pev_kod_7' => null,
            'pev_kod_8' => null,
            'pev_kod_9' => null,
            'pev_kod_10' => null,
            'kod_skupiny_spoju' => null,
        ];
    }

    /** Trip number belonging to the outbound direction (odd numbers per the JDF convention). */
    public function outbound(): static
    {
        return $this->state(fn() => ['cislo_spoje' => $this->faker->unique()->numberBetween(1, 200) * 2 - 1]);
    }

    /** Trip number belonging to the return direction (even numbers per the JDF convention). */
    public function returning(): static
    {
        return $this->state(fn() => ['cislo_spoje' => $this->faker->unique()->numberBetween(1, 200) * 2]);
    }
}
