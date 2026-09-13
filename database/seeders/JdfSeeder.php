<?php

namespace Database\Seeders;

use App\Models\AltDopravca;
use App\Models\AltLinka;
use App\Models\CasKod;
use App\Models\Dopravca;
use App\Models\Linka;
use App\Models\Mistenka;
use App\Models\Navaznost;
use App\Models\PevnyKod;
use App\Models\Spoj;
use App\Models\Udaj;
use App\Models\VerzeJdf;
use App\Models\ZasLinka;
use App\Models\ZasSpoj;
use App\Models\Zastavka;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Rebuilds a representative JDF batch modeled on the real "207101-207111" export
 * (ARRIVA Trnava, a.s. / rozliseni_linky 46266 / JDF 1.11).
 *
 * Line 207101 is seeded with the ACTUAL ZasLinky stop sequence and the actual
 * timetable for Spoj č. 1 taken from the uploaded files. Everything else
 * (other spoje, other lines' stop sequences, AltDop/Navaznosti/Udaje/Mistenky/
 * AltLinky/LinExt/SpojSkup) is synthetic, generated via the factories, since
 * the real batch either didn't include those files or was too large to
 * transcribe in full — it's there to exercise every table and every
 * relationship in the schema.
 */
class JdfSeeder extends Seeder
{
    private const ROZLISENI_LINKY = 46266;
    private const ROZLISENI_DOPRAVCE = 0;

    public function run(): void
    {
        $this->seedBatchHeader();
        $this->seedPevneKody();
        $dopravca = $this->seedDopravca();
        $this->seedZastavky();

        // --- Line 207101: real ZasLinky sequence + a faithfully-reconstructed Spoj č. 1 ---
        $linka101 = $this->seedLinka(207101, $dopravca, 'Trnava - Zeleneč');
        $zasLinky101 = $this->seedZasLinky($linka101, $this->realZasLinkyRows207101());
        $this->seedRealSpoj1($linka101, $zasLinky101);
        $this->seedRealCasKodyForSpoj1($linka101);
        $this->seedSyntheticTrips($linka101, $zasLinky101, tripCount: 6, startSpojNumber: 3);

        // --- Remaining lines: synthetic but schema-faithful timetables ---
        $otherLines = [
            207104 => 'Trnava - Zavar',
            207106 => 'Trnava - Hlohovec',
            207111 => 'Trnava - Suchá nad Parnou',
        ];

        foreach ($otherLines as $cisloLinky => $nazev) {
            $linka = $this->seedLinka($cisloLinky, $dopravca, $nazev);
            $zasLinky = $this->seedZasLinky($linka, $this->randomZasLinkyRows(rand(14, 22)));
            $this->seedSyntheticTrips($linka, $zasLinky, tripCount: 8, startSpojNumber: 1);
        }

        // --- Optional files: empty in the real batch, seeded here only to exercise the schema ---
        $this->seedOptionalExtras($linka101, $dopravca);
    }

    // ---------------------------------------------------------------
    // Header / lookups / carriers / stops
    // ---------------------------------------------------------------

    private function seedBatchHeader(): void
    {
        VerzeJdf::factory()->create([
            'cislo_verze_jdf' => '1.11',
            'cislo_du' => null,
            'okres_kraj' => null,
            'identifikace_davky' => '2026',
            'datum_vyroby_davky' => Carbon::createFromFormat('dmY', '26082026'),
            'jmeno' => null,
        ]);
    }

    private function seedPevneKody(): void
    {
        // Pevnykod.txt wasn't part of the upload, so the real number -> symbol
        // mapping for these ids is unknown. These are plausible placeholders,
        // needed only so the pev_kod_* foreign keys used below resolve.
        $codes = [
            '10' => 'X', // jede v pracovních dnech (placeholder)
            '11' => '+', // jede v neděli a ve státem uznané svátky (placeholder)
            '17' => '6', // jede v sobotu (placeholder)
            '29' => 'x', // zastávka je jen na znamení nebo požádání (placeholder)
        ];

        foreach ($codes as $cislo => $symbol) {
            PevnyKod::factory()->create([
                'cislo_pevneho_kodu' => $cislo,
                'oznaceni_pevneho_kodu' => $symbol,
            ]);
        }
    }

    private function seedDopravca(): Dopravca
    {
        // Reproduced verbatim from the uploaded Dopravci.txt.
        return Dopravca::factory()->create([
            'ic' => '00008140',
            'dic' => null,
            'obchodni_jmeno' => 'ARRIVA Trnava, a.s.',
            'druh_firmy' => 1,
            'jmeno_fyz_osoby' => null,
            'sidlo' => 'Nitrianska 5, 917 02 Trnava',
            'telefon_sidla' => 'info 0915 733 733',
            'telefon_dispecink' => null,
            'telefon_informace' => 'Infolinka: 0915 733 733,www.arriva.sk/trnava',
            'fax' => null,
            'email' => null,
            'www' => null,
            'rozliseni_dopravce' => self::ROZLISENI_DOPRAVCE,
        ]);
    }

    /** Every stop number actually referenced across the four uploaded ZasLinky.txt lines. */
    private function seedZastavky(): void
    {
        $cislaZastavek = [
            63501,
            63502,
            63503,
            63504,
            63505,
            63506,
            63507,
            63508,
            63509,
            63510,
            63517,
            63519,
            63523,
            63524,
            63525,
            63526,
            63527,
            63528,
            63530,
            63540,
            63541,
            63544,
            63545,
            63546,
            63567,
            63569,
            63570,
            63571,
            63572,
            63573,
            63574,
            63576,
            63577,
            63578,
            63579,
            63581,
            63585,
            63587,
            63588,
            63593,
            63600,
            63604,
            63606,
            63616,
            63629,
            63640,
            63641,
            63643,
            63645,
            63650,
        ];

        foreach ($cislaZastavek as $cislo) {
            Zastavka::factory()->create(['cislo_zastavky' => $cislo]);
        }
    }

    // ---------------------------------------------------------------
    // Lines and their stop sequences
    // ---------------------------------------------------------------

    private function seedLinka(int $cisloLinky, Dopravca $dopravca, string $nazevLinky): Linka
    {
        return Linka::factory()->create([
            'cislo_linky' => $cisloLinky,
            'rozliseni_linky' => self::ROZLISENI_LINKY,
            'nazev_linky' => $nazevLinky,
            'ic_dopravce' => $dopravca->ic,
            'rozliseni_dopravce' => $dopravca->rozliseni_dopravce,
        ]);
    }

    /** @return Collection<int, ZasLinka> keyed by array position, in tarifni order */
    private function seedZasLinky(Linka $linka, array $rows): Collection
    {
        return collect($rows)->map(fn(array $row) => ZasLinka::factory()->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
            'cislo_tarifni' => $row['tarifni'],
            'cislo_zastavky' => $row['zastavka'],
            'pev_kod_1' => $row['pev_kod_1'] ?? null,
        ]));
    }

    /** The real Zaslinky.txt rows for line 207101 (25 stops). */
    private function realZasLinkyRows207101(): array
    {
        $raw = [
            [1, 63524],
            [2, 63570],
            [3, 63501],
            [4, 63585],
            [5, 63573],
            [6, 63574, '29'],
            [7, 63640, '29'],
            [8, 63505],
            [9, 63641],
            [10, 63506],
            [11, 63517],
            [12, 63507],
            [13, 63508],
            [14, 63509],
            [15, 63510],
            [16, 63579],
            [17, 63606],
            [18, 63587],
            [19, 63588],
            [20, 63581, '29'],
            [21, 63523],
            [22, 63523],
            [23, 63629],
            [24, 63519, '29'],
            [25, 63571],
        ];

        return array_map(fn(array $r) => [
            'tarifni' => $r[0],
            'zastavka' => $r[1],
            'pev_kod_1' => $r[2] ?? null,
        ], $raw);
    }

    /** A plausible (synthetic) stop sequence for lines without transcribed real data. */
    private function randomZasLinkyRows(int $count): array
    {
        $pool = [
            63501,
            63502,
            63503,
            63504,
            63505,
            63506,
            63507,
            63508,
            63509,
            63510,
            63517,
            63519,
            63523,
            63524,
            63525,
            63526,
            63527,
            63528,
            63540,
            63541,
            63569,
            63570,
            63571,
            63572,
            63573,
            63574,
            63579,
            63581,
            63585,
            63587,
            63588,
            63600,
            63604,
            63606,
            63616,
            63629,
            63640,
            63641,
            63645,
            63650,
        ];

        $stops = collect($pool)->shuffle()->take($count)->values();

        return $stops->map(fn($zastavka, $i) => [
            'tarifni' => $i + 1,
            'zastavka' => $zastavka,
        ])->all();
    }

    // ---------------------------------------------------------------
    // Trips (Spoje + Zasspoje)
    // ---------------------------------------------------------------

    /**
     * Reconstructs the real timetable for Spoj č. 1 of line 207101 (tariff 3-14),
     * taken directly from the uploaded ZasSpoje.txt — including its pass-through
     * stop (č. 63641) and its terminus (arrival only, no departure).
     */
    private function seedRealSpoj1(Linka $linka, Collection $zasLinky): void
    {
        Spoj::factory()->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
            'cislo_spoje' => 1,
            'pev_kod_1' => '10',
        ]);

        $stops = [
            ['tarifni' => 3, 'zastavka' => 63501, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0406'],
            ['tarifni' => 4, 'zastavka' => 63585, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0407'],
            ['tarifni' => 5, 'zastavka' => 63573, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0408'],
            ['tarifni' => 6, 'zastavka' => 63574, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0409'],
            ['tarifni' => 7, 'zastavka' => 63640, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0410'],
            ['tarifni' => 8, 'zastavka' => 63505, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0411'],
            ['tarifni' => 9, 'zastavka' => 63641, 'passthrough' => true],
            ['tarifni' => 10, 'zastavka' => 63506, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0414'],
            ['tarifni' => 11, 'zastavka' => 63517, 'oznacnik' => 1, 'stanoviste' => 'C', 'cas_odjezdu' => '0416'],
            ['tarifni' => 12, 'zastavka' => 63507, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0417'],
            ['tarifni' => 13, 'zastavka' => 63508, 'oznacnik' => 1, 'stanoviste' => 'A', 'cas_odjezdu' => '0419'],
            ['tarifni' => 14, 'zastavka' => 63509, 'oznacnik' => 1, 'stanoviste' => 'B', 'cas_prijezdu' => '0420'], // terminus
        ];

        foreach ($stops as $stop) {
            $attributes = [
                'cislo_linky' => $linka->cislo_linky,
                'rozliseni_linky' => $linka->rozliseni_linky,
                'cislo_spoje' => 1,
                'cislo_tarifni' => $stop['tarifni'],
                'cislo_zastavky' => $stop['zastavka'],
            ];

            if (! empty($stop['passthrough'])) {
                ZasSpoj::factory()->passesThrough()->create($attributes);

                continue;
            }

            ZasSpoj::factory()->create($attributes + [
                'kod_oznacniku' => $stop['oznacnik'],
                'cislo_stanoviste' => $stop['stanoviste'],
                'cas_prijezdu' => $stop['cas_prijezdu'] ?? null,
                'cas_odjezdu' => $stop['cas_odjezdu'] ?? null,
            ]);
        }
    }

    /** Real Caskody.txt rows tied to Spoj č. 1 of line 207101. */
    private function seedRealCasKodyForSpoj1(Linka $linka): void
    {
        CasKod::factory()->infoNote('ide po zastávku žel. stanica')->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
            'cislo_spoje' => 1,
            'cislo_casoveho_kodu' => 1,
        ]);
    }

    /**
     * Generates additional trips over an already-seeded stop sequence, alternating
     * outbound/return direction and occasionally marking a mid-route stop as
     * pass-through — mirroring the pattern seen in the real ZasSpoje.txt.
     */
    private function seedSyntheticTrips(Linka $linka, Collection $zasLinky, int $tripCount, int $startSpojNumber): void
    {
        $cisloSpoje = $startSpojNumber;

        for ($i = 0; $i < $tripCount; $i++) {
            $outbound = $cisloSpoje % 2 === 1;
            $stops = $outbound ? $zasLinky : $zasLinky->reverse()->values();

            Spoj::factory()->create([
                'cislo_linky' => $linka->cislo_linky,
                'rozliseni_linky' => $linka->rozliseni_linky,
                'cislo_spoje' => $cisloSpoje,
                'pev_kod_1' => '10',
            ]);

            $minute = random_int(0, 59);
            $hour = random_int(5, 20);
            $last = $stops->count() - 1;

            foreach ($stops as $index => $zasLinka) {
                $attributes = [
                    'cislo_linky' => $linka->cislo_linky,
                    'rozliseni_linky' => $linka->rozliseni_linky,
                    'cislo_spoje' => $cisloSpoje,
                    'cislo_tarifni' => $zasLinka->cislo_tarifni,
                    'cislo_zastavky' => $zasLinka->cislo_zastavky,
                ];

                if ($index !== 0 && $index !== $last && random_int(1, 10) === 1) {
                    ZasSpoj::factory()->passesThrough()->create($attributes);

                    continue;
                }

                $minute += random_int(1, 4);
                $hour += intdiv($minute, 60);
                $minute %= 60;
                $time = sprintf('%02d%02d', $hour, $minute);

                ZasSpoj::factory()->create($attributes + [
                    'kod_oznacniku' => 1,
                    'cislo_stanoviste' => $this->faker()->randomElement(['A', 'B', 'C']),
                    'cas_prijezdu' => $index === $last ? $time : null,
                    'cas_odjezdu' => $index === $last ? null : $time,
                ]);
            }

            $cisloSpoje++;
        }
    }

    // ---------------------------------------------------------------
    // Optional files (empty in the real batch — seeded here for schema coverage)
    // ---------------------------------------------------------------

    private function seedOptionalExtras(Linka $linka, Dopravca $dopravca): void
    {
        Udaj::factory()->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
            'cislo_udaje' => 1,
            'text' => 'Bezbariérový prístup na vybraných spojoch.',
        ]);

        AltDopravca::factory()->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
            'ic_dopravce' => $dopravca->ic,
            'rozliseni_dopravce' => $dopravca->rozliseni_dopravce,
        ]);

        Navaznost::factory()->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
            'cislo_spoje' => 1,
            'cislo_tarifni' => 14,
        ]);

        Mistenka::factory()->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
        ]);

        AltLinka::factory()->create([
            'cislo_linky' => $linka->cislo_linky,
            'rozliseni_linky' => $linka->rozliseni_linky,
        ]);
    }

    private function faker(): \Faker\Generator
    {
        return \Faker\Factory::create();
    }
}
