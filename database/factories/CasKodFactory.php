<?php

namespace Database\Factories;

use App\Models\CasKod;
use Illuminate\Database\Eloquent\Factories\Factory;

class CasKodFactory extends Factory
{
    protected $model = CasKod::class;

    public function definition(): array
    {
        // Real Caskody.txt mixed two shapes: date-ranged operating patterns (typ 1-8, e.g. "10","68")
        // and free-text info rows (oznaceni = "p", typ left blank).
        $datumOd = $this->faker->dateTimeBetween('-3 months', '+3 months');

        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_spoje' => null, // pass explicitly
            'cislo_casoveho_kodu' => $this->faker->unique()->numberBetween(1, 12),
            'oznaceni_casoveho_kodu' => (string) $this->faker->numberBetween(10, 79),
            'typ_casove_kodu' => (string) $this->faker->numberBetween(1, 8),
            'datum_od' => $datumOd,
            'datum_do' => $this->faker->optional(0.4)->dateTimeBetween($datumOd, '+6 months'),
            'poznamka' => null,
        ];
    }

    /** Free-text traveller info instead of a date-ranged operating pattern (oznaceni = "p"). */
    public function infoNote(string $text): static
    {
        return $this->state(fn () => [
            'oznaceni_casoveho_kodu' => 'p',
            'typ_casove_kodu' => null,
            'datum_od' => null,
            'datum_do' => null,
            'poznamka' => $text,
        ]);
    }
}
