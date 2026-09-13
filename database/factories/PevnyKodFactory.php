<?php

namespace Database\Factories;

use App\Models\PevnyKod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PevnyKodFactory extends Factory
{
    protected $model = PevnyKod::class;

    /** The fixed-code symbol alphabet defined by the JDF appendix (bod 1a) a 2). */
    private static array $symbols = [
        'X', '+', '1', '2', '3', '4', '5', '6', '7', 'R', '#', '|', '<', '@', '%',
        'W', 'w', 'x', '~', 'I', '(', ')', '$', '{', '}', '[', 'O', 'v', 's',
        '§', 'A', 'B', 'C',
    ];

    public function definition(): array
    {
        return [
            // Real batches assign small, arbitrary sequential ids per-symbol (seen: "10", "11", "17", "29").
            'cislo_pevneho_kodu' => (string) $this->faker->unique()->numberBetween(1, 99),
            'oznaceni_pevneho_kodu' => $this->faker->randomElement(self::$symbols),
            'rezerva' => null,
        ];
    }
}
