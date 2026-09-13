<?php

namespace Database\Factories;

use App\Models\Dopravca;
use App\Models\Linka;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkaFactory extends Factory
{
    protected $model = Linka::class;

    public function definition(): array
    {
        // Real sample line numbers were 207101, 207104, 207106, 207111 (all sharing rozliseni_linky = 46266).
        $platnostOd = $this->faker->dateTimeBetween('-6 months', 'now');

        return [
            'cislo_linky' => $this->faker->unique()->numberBetween(200000, 219999),
            'rozliseni_linky' => 46266,
            'nazev_linky' => $this->faker->city() . ' - ' . $this->faker->city(),
            'ic_dopravce' => Dopravca::factory(),
            'rozliseni_dopravce' => 0,
            'typ_linky' => $this->faker->randomElement(['V', 'V', 'A', 'Z', 'D']),
            'dopravni_prostredek' => $this->faker->randomElement(['A', 'A', 'A', 'T', 'E']),
            'objizdkovy_jr' => false,
            'seskupeni_spoju' => false,
            'pouziti_oznacniku' => $this->faker->boolean(30),
            'rezerva' => null,
            'cislo_licence' => $this->faker->optional(0.5)->numerify('####/####'),
            'platnost_lic_od' => $this->faker->optional(0.5)->dateTimeBetween('-2 years', $platnostOd),
            'platnost_lic_do' => null,
            'platnost_jr_od' => $platnostOd,
            'platnost_jr_do' => null,
        ];
    }
}
