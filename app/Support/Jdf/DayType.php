<?php

namespace App\Support\Jdf;

use Illuminate\Support\Collection;

/**
 * Classifies a Spoj's operating-day pattern from its pev_kod_1..N values,
 * using the fixed-code meanings from the JDF appendix (bod 1a):
 *   X = jede v pracovních dnech, + = jede v neděli a svátky, 1..7 = Po..Ne.
 *
 * A trip can carry several fixed codes at once (e.g. several individual
 * weekdays); classify() returns the first recognised one, which is enough
 * to bucket trips for a filter UI.
 */
class DayType
{
    private const MAP = [
        'X' => ['key' => 'weekday', 'label' => 'Pracovné dni'],
        '+' => ['key' => 'sunday_holiday', 'label' => 'Nedeľa a sviatky'],
        '1' => ['key' => 'mon', 'label' => 'Pondelok'],
        '2' => ['key' => 'tue', 'label' => 'Utorok'],
        '3' => ['key' => 'wed', 'label' => 'Streda'],
        '4' => ['key' => 'thu', 'label' => 'Štvrtok'],
        '5' => ['key' => 'fri', 'label' => 'Piatok'],
        '6' => ['key' => 'sat', 'label' => 'Sobota'],
        '7' => ['key' => 'sun', 'label' => 'Nedeľa'],
    ];

    public const OTHER = ['key' => 'other', 'label' => 'Ostatné'];

    /**
     * @param  array<int, string|null>  $pevneKodyOnSpoj  the trip's pev_kod_1..N values
     * @param  Collection<string, string>  $lookup  cislo_pevneho_kodu => symbol, from the Pevnykod table
     * @return array{key: string, label: string}
     */
    public static function classify(array $pevneKodyOnSpoj, Collection $lookup): array
    {
        foreach ($pevneKodyOnSpoj as $kod) {
            if (! $kod) {
                continue;
            }

            $symbol = $lookup->get($kod);

            if ($symbol !== null && isset(self::MAP[$symbol])) {
                return self::MAP[$symbol];
            }
        }

        return self::OTHER;
    }

    /** All possible buckets, in display order, for building a filter's option list. */
    public static function all(): array
    {
        return array_merge(self::MAP, ['other' => self::OTHER]);
    }
}
