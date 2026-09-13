<?php

namespace App\Http\Controllers;

use App\Models\Dopravca;
use App\Models\Linka;
use App\Models\PevnyKod;
use App\Models\Spoj;
use App\Models\ZasLinka;
use App\Models\ZasSpoj;
use App\Models\Zastavka;
use App\Support\Jdf\DayType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkaTimetableController extends Controller
{
    /** Board of all lines in the batch. */
    public function index(): View
    {
        $linky = Linka::orderBy('cislo_linky')->get();

        $dopravcovia = Dopravca::whereIn('ic', $linky->pluck('ic_dopravce'))
            ->get()
            ->keyBy(fn (Dopravca $d) => $d->ic . '|' . $d->rozliseni_dopravce);

        return view('linky.index', compact('linky', 'dopravcovia'));
    }

    /**
     * Full stop x trip timetable grid for one line, in the classic
     * "stops down the side, trips across the top" layout.
     */
    public function show(Request $request, int $cisloLinky, int $rozliseniLinky): View
    {
        $linka = Linka::where('cislo_linky', $cisloLinky)
            ->where('rozliseni_linky', $rozliseniLinky)
            ->firstOrFail();

        $dopravca = Dopravca::where('ic', $linka->ic_dopravce)
            ->where('rozliseni_dopravce', $linka->rozliseni_dopravce)
            ->first();

        // Ordered stop sequence for this line (the left-hand column of the grid).
        $zasLinky = ZasLinka::where('cislo_linky', $cisloLinky)
            ->where('rozliseni_linky', $rozliseniLinky)
            ->orderBy('cislo_tarifni')
            ->get();

        $zastavky = Zastavka::whereIn('cislo_zastavky', $zasLinky->pluck('cislo_zastavky'))
            ->get()
            ->keyBy('cislo_zastavky');

        // Trips (the columns of the grid).
        $spoje = Spoj::where('cislo_linky', $cisloLinky)
            ->where('rozliseni_linky', $rozliseniLinky)
            ->orderBy('cislo_spoje')
            ->get();

        // All timetable rows for this line, indexed [cislo_spoje][cislo_tarifni] for O(1) grid lookups.
        $zasSpoje = ZasSpoj::where('cislo_linky', $cisloLinky)
            ->where('rozliseni_linky', $rozliseniLinky)
            ->get()
            ->groupBy('cislo_spoje')
            ->map(fn ($rows) => $rows->keyBy('cislo_tarifni'));

        // Fixed-code symbols, so a trip's pev_kod_* can show its operating-day badge and be filtered.
        $pevneKody = PevnyKod::pluck('oznaceni_pevneho_kodu', 'cislo_pevneho_kodu');

        // Classify every trip by operating-day pattern (weekday, Saturday, Sunday & holidays, ...).
        $spoje = $spoje->map(function (Spoj $spoj) use ($pevneKody) {
            $kody = collect(range(1, 10))
                ->map(fn ($n) => $spoj->{"pev_kod_{$n}"})
                ->all();

            $spoj->day_type = DayType::classify($kody, $pevneKody);

            return $spoj;
        });

        // Only offer filter options for day types that actually occur on this line.
        $availableDayTypes = $spoje->pluck('day_type.key')->unique();
        $dayTypeOptions = collect(DayType::all())
            ->filter(fn ($type) => $availableDayTypes->contains($type['key']))
            ->values();

        $selectedDayType = $request->query('day', 'all');

        if ($selectedDayType !== 'all') {
            $spoje = $spoje->filter(fn (Spoj $spoj) => $spoj->day_type['key'] === $selectedDayType)->values();
        }

        return view('linky.show', compact(
            'linka', 'dopravca', 'zasLinky', 'zastavky', 'spoje', 'zasSpoje',
            'pevneKody', 'dayTypeOptions', 'selectedDayType'
        ));
    }
}
