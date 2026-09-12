<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cestovné poriadky')</title>
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
            --radius: 3px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font-family: 'Work Sans', sans-serif;
            font-size: 15px;
            line-height: 1.5;
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
    @yield('content')
</body>
</html>
