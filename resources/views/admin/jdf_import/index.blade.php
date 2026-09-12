@extends('layouts.app')

@section('title', 'Import JDF dávky')

@push('styles')
<style>
    .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(9rem, 1fr));
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    .status-card {
        background: #fff;
        border: 1px solid var(--rule);
        border-radius: var(--radius);
        padding: 0.85rem 1rem;
    }

    .status-card__count {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 1.4rem;
        color: var(--board);
    }

    .status-card__label {
        font-size: 0.78rem;
        color: var(--ink-soft);
    }

    .status-card--empty .status-card__count { color: var(--rule); }

    .panel {
        background: #fff;
        border: 1px solid var(--rule);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .panel h2 { margin-top: 0; font-size: 1.3rem; }

    .field-row { margin-bottom: 1rem; }

    input[type="file"] {
        display: block;
        width: 100%;
        padding: 0.6rem;
        border: 1px dashed var(--rule);
        border-radius: var(--radius);
        background: var(--paper);
    }

    .checkbox-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 1rem 0;
    }

    .btn {
        display: inline-block;
        background: var(--board);
        color: var(--board-ink);
        border: none;
        padding: 0.65rem 1.4rem;
        border-radius: var(--radius);
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 600;
        font-size: 1.05rem;
        cursor: pointer;
    }

    .btn:hover { background: #1c3e70; }

    .hint { color: var(--ink-soft); font-size: 0.85rem; }

    table.results {
        border-collapse: collapse;
        width: 100%;
        font-size: 0.9rem;
    }

    table.results th, table.results td {
        text-align: left;
        padding: 0.5rem 0.6rem;
        border-bottom: 1px solid var(--rule);
    }

    table.results th { border-bottom: 2px solid var(--ink); }

    .num { font-family: 'IBM Plex Mono', monospace; text-align: right; }

    .badge-ok { color: #2E7D32; font-weight: 600; }
    .badge-warn { color: var(--reroute); font-weight: 600; }

    .warning-list { margin: 0.25rem 0 0; padding-left: 1.1rem; color: var(--ink-soft); font-size: 0.82rem; }

    .alert {
        background: #FBEFC0;
        border: 1px solid var(--weekday);
        border-radius: var(--radius);
        padding: 0.85rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .selected-files {
        list-style: none;
        margin: 0.6rem 0 0;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .selected-files li {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.78rem;
        background: var(--paper);
        border: 1px solid var(--rule);
        border-radius: 999px;
        padding: 0.2rem 0.7rem;
        color: var(--ink-soft);
    }
</style>
@endpush

@section('content')
    <div class="board-bar">
        <p class="board-bar__eyebrow"><a class="back-link" href="{{ route('linky.index') }}">&larr; Cestovné poriadky</a></p>
        <h1>Import JDF dávky</h1>
    </div>

    <main>
        <h2 style="font-family:'Barlow Condensed',sans-serif;">Aktuálny stav databázy</h2>
        <div class="status-grid">
            @foreach ($tableCounts as $status)
                <div class="status-card {{ $status['count'] === 0 ? 'status-card--empty' : '' }}">
                    <div class="status-card__count">{{ $status['count'] }}</div>
                    <div class="status-card__label">{{ $status['table'] }}</div>
                </div>
            @endforeach
        </div>

        @if (! empty($unmatched))
            <div class="alert">
                Tieto súbory neboli rozpoznané ako platné JDF súbory a boli preskočené:
                <strong>{{ implode(', ', $unmatched) }}</strong>
            </div>
        @endif

        @if ($results)
            <div class="panel">
                <h2>Výsledok importu</h2>
                <table class="results">
                    <thead>
                        <tr>
                            <th>Súbor</th>
                            <th>Tabuľka</th>
                            <th class="num">Riadkov v súbore</th>
                            <th class="num">Importovaných</th>
                            <th class="num">Preskočených</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $result)
                            <tr>
                                <td>{{ $result['key'] }}</td>
                                <td>{{ $result['table'] }}</td>
                                <td class="num">{{ $result['total'] }}</td>
                                <td class="num {{ $result['imported'] > 0 ? 'badge-ok' : '' }}">{{ $result['imported'] }}</td>
                                <td class="num {{ $result['skipped'] > 0 ? 'badge-warn' : '' }}">{{ $result['skipped'] }}</td>
                            </tr>
                            @if (! empty($result['warnings']))
                                <tr>
                                    <td colspan="5">
                                        <ul class="warning-list">
                                            @foreach ($result['warnings'] as $warning)
                                                <li>{{ $warning }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="panel">
            <h2>Nahrať súbory</h2>
            <p class="hint">
                Vyberte ľubovoľný počet súborov z JDF dávky (napr. Linky.txt, ZasLinky.txt, Spoje.txt, ZasSpoje.txt, ...).
                Súbory sa podľa názvu automaticky priradia k správnej tabuľke a naimportujú v bezpečnom poradí bez ohľadu
                na to, ako ich vyberiete.
            </p>

            @if ($errors->any())
                <div class="alert">
                    <ul style="margin:0;padding-left:1.1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.jdf-import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="field-row">
                    <input type="file" name="files[]" multiple accept=".txt" id="jdf-files">
                    <ul id="jdf-selected-files" class="selected-files"></ul>
                </div>

                <script>
                    // Native multi-file inputs REPLACE the previous selection every time you
                    // reopen the dialog — they don't accumulate. This just makes that visible
                    // before you submit, since silently missing a file (e.g. Dopravci.txt)
                    // causes a downstream foreign-key error that's easy to misread.
                    document.getElementById('jdf-files').addEventListener('change', function (e) {
                        const list = document.getElementById('jdf-selected-files');
                        list.innerHTML = '';

                        if (e.target.files.length === 0) {
                            return;
                        }

                        Array.from(e.target.files)
                            .sort((a, b) => a.name.localeCompare(b.name))
                            .forEach(file => {
                                const li = document.createElement('li');
                                li.textContent = file.name;
                                list.appendChild(li);
                            });
                    });
                </script>
                <div class="checkbox-row">
                    <input type="checkbox" id="replace" name="replace" value="1" checked>
                    <label for="replace">
                        Nahradiť existujúce dáta (vymaže všetky JDF tabuľky pred importom — nahrávajte vždy kompletnú dávku)
                    </label>
                </div>

                <button type="submit" class="btn">Importovať</button>
            </form>
        </div>
    </main>
@endsection
