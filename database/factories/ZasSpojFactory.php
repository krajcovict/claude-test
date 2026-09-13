<?php

namespace Database\Factories;

use App\Models\ZasSpoj;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZasSpojFactory extends Factory
{
    protected $model = ZasSpoj::class;

    /** Random HH:MM-ish 4-digit time string, matching the "0406" style seen in the real file. */
    private function time(int $hour): string
    {
        return sprintf('%02d%02d', $hour, $this->faker->numberBetween(0, 59));
    }

    public function definition(): array
    {
        $hour = $this->faker->numberBetween(5, 21);

        return [
            'cislo_linky' => null, // pass explicitly — see usage note
            'rozliseni_linky' => 46266,
            'cislo_spoje' => null, // pass explicitly
            'cislo_tarifni' => null, // pass explicitly
            'cislo_zastavky' => null, // pass explicitly
            'kod_oznacniku' => $this->faker->optional(0.6)->numberBetween(1, 3),
            'cislo_stanoviste' => $this->faker->optional(0.5)->randomElement(['A', 'B', 'C']),
            'pev_kod_1' => null,
            'pev_kod_2' => null,
            'kilometry' => $this->faker->randomFloat(3, 0, 45),
            'cas_prijezdu' => null,
            'cas_odjezdu' => $this->time($hour),
        ];
    }

    /** Stop is skipped: "spoj zastávkou projíždí" — both time fields carry the literal "|". */
    public function passesThrough(): static
    {
        return $this->state(fn () => [
            'cas_prijezdu' => '|',
            'cas_odjezdu' => '|',
            'kilometry' => null,
        ]);
    }

    /** Trip runs a different route here: "spoj jede po jiné trase" — both time fields carry "<". */
    public function reroute(): static
    {
        return $this->state(fn () => [
            'cas_prijezdu' => '<',
            'cas_odjezdu' => '<',
            'kilometry' => null,
        ]);
    }

    /** Layover longer than 5 minutes: both arrival and departure times are given. */
    public function withLayover(): static
    {
        $hour = $this->faker->numberBetween(5, 21);

        return $this->state(fn () => [
            'cas_prijezdu' => $this->time($hour),
            'cas_odjezdu' => $this->time($hour),
        ]);
    }
}
