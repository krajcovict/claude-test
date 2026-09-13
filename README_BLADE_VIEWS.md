# JDF timetable Blade views

## Files in this package
- `app/Http/Controllers/LinkaTimetableController.php` — `index()` lists every line, `show()` builds the stop × trip grid for one line.
- `app/View/Components/JdfTime.php` + `resources/views/components/jdf-time.blade.php` — turns a raw Zasspoje time value ("0406", "|", "<", or empty) into a readable label.
- `resources/views/layouts/app.blade.php` — shared page shell/styles (Barlow Condensed for headings, Work Sans for body, IBM Plex Mono for the time grid).
- `resources/views/linky/index.blade.php` — the line list ("departure board").
- `resources/views/linky/show.blade.php` — the actual timetable: stops down the left (sticky column), trips across the top, times in the grid.
- `routes/web.php` — **merge** the two `Route::get(...)` lines into your existing routes file; don't replace it wholesale, since it'll only contain those two routes otherwise.

## Why cisloLinky/rozliseniLinky are both in the URL
None of the JDF tables have a single-column primary key — lines are identified by `cislo_linky` + `rozliseni_linky` together — and Eloquent doesn't support composite route-model binding out of the box. So `show()` takes both segments and looks the row up manually, e.g.:

    /linky/207101/46266

## Try it
1. Copy `app/`, `resources/`, and the two routes into your project.
2. Make sure you've run the migrations and the `DatabaseSeeder` from earlier (it seeds line 207101 with the real reconstructed timetable for Spoj č. 1).
3. `php artisan serve`, then visit `/linky` and click into a line.

## Notes / things you may want to adjust
- The "T"/"Z" direction badge is just the odd/even convention from the JDF spec (odd = outbound, even = return) — purely a display hint, not stored data.
- The small badge under each trip's number shows `pevnykod.oznaceni_pevneho_kodu` for that trip's `pev_kod_1` — remember those symbols are placeholders (see the seeder notes), so treat the badge as illustrative until you load a real `Pevnykod.txt`.
- A line with a lot of trips will make the grid wide; the wrapper scrolls horizontally with the stop names pinned via `position: sticky`, but for very large batches you may want to paginate trips or split by day-type instead of rendering every `Spoj` in one table.
