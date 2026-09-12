@extends('layouts.app')

@section('title', 'Cestovné poriadky')

@push('styles')
<style>
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(16rem, 1fr));
        gap: 1.25rem;
    }

    .home-card {
        display: block;
        background: var(--surface);
        border: 1px solid var(--rule);
        border-radius: var(--radius);
        padding: 1.5rem;
        transition: border-color 120ms ease, transform 120ms ease;
    }

    .home-card:hover {
        border-color: var(--board);
        transform: translateY(-2px);
    }

    .home-card h2 {
        margin: 0 0 0.5rem;
        font-size: 1.4rem;
        color: var(--board);
    }

    .home-card p {
        margin: 0;
        color: var(--ink-soft);
        font-size: 0.92rem;
    }
</style>
@endpush

@section('content')
    <div class="board-bar">
        <p class="board-bar__eyebrow">Cestovné poriadky</p>
        <h1>Prehľad aplikácie</h1>
    </div>

    <main>
        <div class="card-grid">
            <a class="home-card" href="{{ route('linky.index') }}">
                <h2>Linky</h2>
                <p>Prehľad liniek a ich cestovných poriadkov.</p>
            </a>

            <a class="home-card" href="{{ route('admin.jdf-import.index') }}">
                <h2>Import JDF dávky</h2>
                <p>Nahrať a spracovať súbory z JDF dávky.</p>
            </a>
        </div>
    </main>
@endsection
