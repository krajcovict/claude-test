<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cestovné poriadky')</title>
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            if (stored === 'dark' || stored === 'light') {
                document.documentElement.setAttribute('data-theme', stored);
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Work+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --paper: #F6F1DE;
            --ink: #14213D;
            --ink-soft: #46567A;
            --rule: #E3D9AE;
            --board: #14315E;
            --board-ink: #F6F1DE;
            --board-soft: #A9C2E8;
            --weekday: #C9A227;
            --through: #8B8478;
            --reroute: #A23E32;
            --surface: #FFFFFF;
            --hover-tint: rgba(16, 36, 63, 0.05);
            --board-hover: #1C3E70;
            --ok: #2E7D32;
            --alert-bg: #FBEFC0;
            --radius: 3px;
        }

        @media (prefers-color-scheme: dark) {
            :root:not([data-theme="light"]) {
                --paper: #1A1D24;
                --ink: #E8E6DE;
                --ink-soft: #A6ADC4;
                --rule: #333844;
                --board: #1B3A66;
                --board-soft: #A9C2E8;
                --weekday: #E8C34D;
                --through: #9C9486;
                --reroute: #E2685A;
                --surface: #242832;
                --hover-tint: rgba(255, 255, 255, 0.06);
                --board-hover: #2A4A82;
                --ok: #4CAF50;
                --alert-bg: #3A331A;
            }
        }

        :root[data-theme="dark"] {
            --paper: #1A1D24;
            --ink: #E8E6DE;
            --ink-soft: #A6ADC4;
            --rule: #333844;
            --board: #1B3A66;
            --board-soft: #A9C2E8;
            --weekday: #E8C34D;
            --through: #9C9486;
            --reroute: #E2685A;
            --surface: #242832;
            --hover-tint: rgba(255, 255, 255, 0.06);
            --board-hover: #2A4A82;
            --ok: #4CAF50;
            --alert-bg: #3A331A;
        }

        * { box-sizing: border-box; }

        .top-bar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem clamp(1rem, 4vw, 3rem);
        }

        .home-link,
        .theme-toggle {
            background: var(--surface);
            color: var(--ink);
            border: 1px solid var(--rule);
            border-radius: 999px;
            padding: 0.4rem 0.9rem;
            font-family: 'Work Sans', sans-serif;
            font-size: 0.8rem;
        }

        .theme-toggle { margin-left: auto; cursor: pointer; }

        .home-link:hover,
        .theme-toggle:hover { border-color: var(--board-soft); }

        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font-family: 'Work Sans', sans-serif;
            font-size: 15px;
            line-height: 1.5;
            transition: background-color 120ms ease, color 120ms ease;
        }

        h1, h2, h3, .shield, .spoj-head__num {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        a { color: inherit; text-decoration: none; }

        .board-bar {
            background: var(--board);
            color: var(--board-ink);
            padding: 1.75rem clamp(1rem, 4vw, 3rem);
        }

        .board-bar__eyebrow {
            color: var(--board-soft);
            font-size: 0.85rem;
            margin: 0 0 0.35rem;
        }

        .board-bar h1 {
            margin: 0;
            font-size: clamp(1.8rem, 4vw, 2.6rem);
        }

        main {
            padding: clamp(1rem, 4vw, 3rem);
            max-width: 1100px;
            margin: 0 auto;
        }

        .rule { border: none; border-top: 1px solid var(--rule); margin: 1.75rem 0; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="top-bar">
        @unless (request()->routeIs('home'))
            <a href="{{ route('home') }}" class="home-link">&larr; Domov</a>
        @endunless
        <button type="button" id="theme-toggle" class="theme-toggle">Tmavý režim</button>
    </div>

    @yield('content')

    <script>
        (function () {
            var toggle = document.getElementById('theme-toggle');

            var currentTheme = function () {
                return document.documentElement.getAttribute('data-theme')
                    || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            };

            var updateLabel = function () {
                toggle.textContent = currentTheme() === 'dark' ? 'Svetlý režim' : 'Tmavý režim';
            };

            updateLabel();

            toggle.addEventListener('click', function () {
                var next = currentTheme() === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
                updateLabel();
            });
        })();
    </script>
</body>
</html>
