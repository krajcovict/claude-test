<?php

namespace Database\Factories;

use App\Models\Zastavka;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZastavkaFactory extends Factory
{
    protected $model = Zastavka::class;

    /** A handful of real towns from the Trnava region the sample data comes from. */
    private static array $obce = [
        'Trnava', 'Hlohovec', 'Piešťany', 'Sereď', 'Leopoldov', 'Suchá nad Parnou',
        'Zeleneč', 'Bučany', 'Boleráz', 'Špačince', 'Zavar', 'Voderady', 'Križovany nad Dudváhom',
    ];

    public function definition(): array
    {
        return [
            // Real files used 5-digit numbers in the 63500-63700 range for this batch.
            'cislo_zastavky' => $this->faker->unique()->numberBetween(63000, 69999),
            'nazev_obce' => $this->faker->randomElement(self::$obce),
            'cast_obce' => $this->faker->optional(0.3)->lastName(),
            'blizsi_misto' => $this->faker->optional(0.15)->streetName(),
            'blizka_obec' => 'SK',
            'stat' => $this->faker->randomElement(['SK', 'SK', 'SK', 'SK', 'CZ', 'AT']),
            'pev_kod_1' => null,
            'pev_kod_2' => null,
            'pev_kod_3' => null,
            'pev_kod_4' => null,
            'pev_kod_5' => null,
            'pev_kod_6' => null,
        ];
    }
}
