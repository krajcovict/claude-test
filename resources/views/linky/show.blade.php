@extends('layouts.app')

@section('title', 'Linka ' . $linka->cislo_linky . ' — ' . $linka->nazev_linky)

@push('styles')
<style>
    .line-head {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 1.25rem;
        align-items: center;
    }

    .line-head .shield {
        background: var(--weekday);
        color: var(--board);
        border-radius: var(--radius);
        padding: 0.5rem 0.9rem;
        font-size: 1.6rem;
        line-height: 1;
    }

    .line-head__name { margin: 0; font-size: clamp(1.5rem, 3.5vw, 2.1rem); }
    .line-head__carrier { color: var(--board-soft); font-size: 0.95rem; margin-top: 0.25rem; }

    .back-link { color: var(--board-soft); font-size: 0.9rem; }
    .back-link:hover { color: var(--board-ink); }

    .grid-wrap {
        overflow-x: auto;
        border: 1px solid var(--rule);
        border-radius: var(--radius);
    }

    table.timetable {
        border-collapse: collapse;
        min-width: 100%;
        font-size: 0.92rem;
    }

    table.timetable th, table.timetable td {
        padding: 0.55rem 0.75rem;
        border-bottom: 1px solid var(--rule);
        white-space: nowrap;
        text-align: left;
    }

    table.timetable thead th {
        background: var(--paper);
        border-bottom: 2px solid var(--ink);
        vertical-align: bottom;
    }

    .stop-col {
        position: sticky;
        left: 0;
        background: var(--paper);
        z-index: 1;
        border-right: 1px solid var(--rule);
        min-width: 12rem;
    }

    thead .stop-col { z-index: 2; }

    .stop-order {
        display: inline-block;
        width: 1.4rem;
        color: var(--ink-soft);
        font-size: 0.8rem;
    }

    .spoj-head { text-align: center; }
    .spoj-head__num { font-size: 1.05rem; }

    .direction {
        display: inline-block;
        width: 1.1rem;
        height: 1.1rem;
        line-height: 1.1rem;
        border-radius: 50%;
        font-size: 0.65rem;
        text-align: center;
        margin-left: 0.3rem;
    }

    .direction--out { background: var(--weekday); color: var(--board); }
    .direction--back { background: var(--board-soft); color: var(--board); }

    .badge {
        display: inline-block;
        margin-top: 0.25rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.75rem;
        color: var(--ink-soft);
    }

    td.time-cell { font-family: 'IBM Plex Mono', monospace; text-align: center; }

    .jt-empty { color: var(--rule); }
    .jt-through { color: var(--through); font-style: italic; font-size: 0.85em; }
    .jt-reroute { color: var(--reroute); font-style: italic; font-size: 0.85em; }

    .legend {
        margin-top: 1rem;
        color: var(--ink-soft);
        font-size: 0.85rem;
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .day-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .day-filter a {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        border: 1px solid var(--board);
        color: var(--board);
        font-size: 0.85rem;
    }

    .day-filter a:hover { background: rgba(20, 49, 94, 0.08); }

    .day-filter a.is-active {
        background: var(--weekday);
        border-color: var(--weekday);
        color: var(--board);
        font-weight: 600;
    }
</style>
@endpush

@section('content')
    <div class="board-bar">
        <p class="board-bar__eyebrow"><a class="back-link" href="{{ route('linky.index') }}">&larr; Všetky linky</a></p>
        <div class="line-head">
            <span class="shield">{{ $linka->cislo_linky }}</span>
            <div>
                <h1 class="line-head__name">{{ $linka->nazev_linky }}</h1>
                @if ($dopravca)
                    <div class="line-head__carrier">{{ $dopravca->obchodni_jmeno }}</div>
                @endif
            </div>
        </div>
    </div>

    <main>
        @if ($dayTypeOptions->isNotEmpty())
            <nav class="day-filter">
                <a href="{{ route('linky.show', [$linka->cislo_linky, $linka->rozliseni_linky]) }}"
                   class="{{ $selectedDayType === 'all' ? 'is-active' : '' }}">
                    Všetky spoje
                </a>
                @foreach ($dayTypeOptions as $option)
                    <a href="{{ route('linky.show', [$linka->cislo_linky, $linka->rozliseni_linky]) }}?day={{ $option['key'] }}"
                       class="{{ $selectedDayType === $option['key'] ? 'is-active' : '' }}">
                        {{ $option['label'] }}
                    </a>
                @endforeach
            </nav>
        @endif

        @if ($zasLinky->isEmpty() || $spoje->isEmpty())
            <p>
                @if ($selectedDayType !== 'all')
                    Pre vybraný typ dňa nie sú na tejto linke žiadne spoje.
                @else
                    Pre túto linku zatiaľ nie sú načítané žiadne zastávky alebo spoje.
                @endif
            </p>
        @else
            <div class="grid-wrap">
                <table class="timetable">
                    <thead>
                        <tr>
                            <th class="stop-col">Zastávka</th>
                            @foreach ($spoje as $spoj)
                                @php $outbound = $spoj->cislo_spoje % 2 === 1; @endphp
                                <th class="spoj-head">
                                    <span class="spoj-head__num">{{ $spoj->cislo_spoje }}</span>
                                    <span class="direction {{ $outbound ? 'direction--out' : 'direction--back' }}"
                                          title="{{ $outbound ? 'smer vedenia linky' : 'smer späť' }}">
                                        {{ $outbound ? 'T' : 'Z' }}
                                    </span>
                                    @if ($spoj->day_type['key'] !== 'other')
                                        <div class="badge">{{ $spoj->day_type['label'] }}</div>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($zasLinky as $poradie => $zas)
                            @php $zastavka = $zastavky->get($zas->cislo_zastavky); @endphp
                            <tr>
                                <td class="stop-col">
                                    <span class="stop-order">{{ $zas->cislo_tarifni }}</span>
                                    {{ $zastavka ? trim($zastavka->nazev_obce . ($zastavka->cast_obce ? ', ' . $zastavka->cast_obce : '')) : ('č. ' . $zas->cislo_zastavky) }}
                                </td>
                                @foreach ($spoje as $spoj)
                                    @php
                                        $cell = $zasSpoje->get($spoj->cislo_spoje)?->get($zas->cislo_tarifni);
                                        $value = $cell?->cas_odjezdu ?: $cell?->cas_prijezdu;
                                    @endphp
                                    <td class="time-cell"><x-jdf-time :value="$value" /></td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="legend">
                <span><span class="direction direction--out">T</span> tam &mdash; smer vedenia linky</span>
                <span><span class="direction direction--back">Z</span> späť</span>
                <span class="jt-through">prechádza &mdash; spoj zastávkou len prechádza</span>
                <span class="jt-reroute">inou trasou &mdash; spoj vedený inak</span>
            </div>
        @endif
    </main>
@endsection
