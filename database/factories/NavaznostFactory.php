<?php

namespace Database\Factories;

use App\Models\Navaznost;
use Illuminate\Database\Eloquent\Factories\Factory;

class NavaznostFactory extends Factory
{
    protected $model = Navaznost::class;

    public function definition(): array
    {
        return [
            'typ_navaznosti' => $this->faker->randomElement(['m', 'M']),
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_spoje' => null, // pass explicitly
            'cislo_tarifni' => null, // pass explicitly
            'cislo_prestupni_linky' => $this->faker->numberBetween(200000, 219999),
            'cislo_zastavky_prestupni_linky' => $this->faker->numberBetween(63000, 69999),
            'kod_oznacniku_prestupni_linky' => $this->faker->optional(0.5)->numberBetween(1, 3),
            'cislo_vych_konc_zast_prestupni' => $this->faker->numberBetween(63000, 69999),
            'kod_vych_konc_oznac_prestupni' => $this->faker->optional(0.5)->numberBetween(1, 3),
            'doba_cekani' => $this->faker->numberBetween(2, 20),
        ];
    }
}
