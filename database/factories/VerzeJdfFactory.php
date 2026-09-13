<?php

namespace Database\Factories;

use App\Models\VerzeJdf;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerzeJdfFactory extends Factory
{
    protected $model = VerzeJdf::class;

    public function definition(): array
    {
        return [
            // Real sample batch was tagged "1.11"; the migrated schema targets 1.10.
            'cislo_verze_jdf' => $this->faker->randomElement(['1.9', '1.10', '1.11']),
            'cislo_du' => $this->faker->optional(0.2)->numberBetween(1, 999),
            'okres_kraj' => $this->faker->optional(0.2)->numerify('##'),
            'identifikace_davky' => (string) $this->faker->numberBetween(2024, 2027), // e.g. "2026"
            'datum_vyroby_davky' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'jmeno' => $this->faker->optional(0.3)->name(),
        ];
    }
}
