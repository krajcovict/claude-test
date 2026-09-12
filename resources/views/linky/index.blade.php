@extends('layouts.app')

@section('title', 'Cestovné poriadky — prehľad liniek')

@push('styles')
<style>
    .line-list {
        list-style: none;
        margin: 0;
        padding: 0;
        border-top: 1px solid var(--rule);
    }

    .line-row {
        display: grid;
        grid-template-columns: 5rem 1fr auto;
        align-items: center;
        gap: 1.25rem;
        padding: 1rem 0.25rem;
        border-bottom: 1px solid var(--rule);
        transition: background 120ms ease;
    }

    .line-row:hover { background: var(--hover-tint); }

    .shield {
        background: var(--weekday);
        color: var(--board);
        border-radius: var(--radius);
        padding: 0.4rem 0;
        text-align: center;
        font-size: 1.15rem;
    }

    .line-row__name { font-size: 1.05rem; }
    .line-row__carrier { color: var(--ink-soft); font-size: 0.9rem; margin-top: 0.15rem; }
    .line-row__meta { color: var(--ink-soft); font-size: 0.85rem; text-align: right; }
</style>
@endpush

@section('content')
    <div class="board-bar">
        <p class="board-bar__eyebrow">Cestovné poriadky</p>
        <h1>Linky v tejto dávke</h1>
    </div>

    <main>
        @if ($linky->isEmpty())
            <p>V databáze zatiaľ nie sú žiadne linky.</p>
        @else
            <ul class="line-list">
                @foreach ($linky as $linka)
                    @php
                        $dopravca = $dopravcovia->get($linka->ic_dopravce . '|' . $linka->rozliseni_dopravce);
                    @endphp
                    <li>
                        <a class="line-row" href="{{ route('linky.show', [$linka->cislo_linky, $linka->rozliseni_linky]) }}">
                            <span class="shield">{{ $linka->cislo_linky }}</span>
                            <span>
                                <span class="line-row__name">{{ $linka->nazev_linky }}</span>
                                @if ($dopravca)
                                    <div class="line-row__carrier">{{ $dopravca->obchodni_jmeno }}</div>
                                @endif
                            </span>
                            <span class="line-row__meta">
                                platí od {{ optional($linka->platnost_jr_od)->format('d.m.Y') }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </main>
@endsection
