<?php

namespace Database\Factories;

use App\Models\Dopravca;
use Illuminate\Database\Eloquent\Factories\Factory;

class DopravcaFactory extends Factory
{
    protected $model = Dopravca::class;

    public function definition(): array
    {
        // Modeled directly on the real "ARRIVA Trnava, a.s." record.
        $druhFirmy = $this->faker->randomElement([1, 1, 1, 2]); // mostly legal entities
        $obchodniJmeno = $this->faker->company() . ', a.s.';

        return [
            'ic' => $this->faker->unique()->numerify('00########'),
            'dic' => $this->faker->optional(0.3)->numerify('SK##########'),
            'obchodni_jmeno' => $obchodniJmeno,
            'druh_firmy' => $druhFirmy,
            'jmeno_fyz_osoby' => $druhFirmy === 2 ? $this->faker->name() : null,
            'sidlo' => $this->faker->streetAddress() . ', ' . $this->faker->postcode() . ' ' . $this->faker->city(),
            'telefon_sidla' => 'info ' . $this->faker->numerify('09## ### ###'),
            'telefon_dispecink' => $this->faker->optional(0.3)->numerify('09## ### ###'),
            'telefon_informace' => $this->faker->optional(0.5)->numerify('Infolinka: 09## ### ###'),
            'fax' => null,
            'email' => $this->faker->optional(0.4)->companyEmail(),
            'www' => $this->faker->optional(0.4)->domainName(),
            'rozliseni_dopravce' => 0,
        ];
    }
}
