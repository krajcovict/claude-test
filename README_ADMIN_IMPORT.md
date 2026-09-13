# JDF admin import panel

## Files
- `config/jdf.php` — maps every JDF filename to its table, column order, type casts, and natural (upsert) key. This is the single source of truth for parsing; adjust it here if a column mapping needs correcting.
- `app/Services/Jdf/JdfLineParser.php` — parses one raw JDF line (quoted, comma-separated, `;`-terminated) into an array of string fields, and converts CP1250 → UTF-8.
- `app/Services/Jdf/JdfFileImporter.php` — imports one file's contents into its table: casts values (dates `DDMMYYYY` → `Y-m-d`, `"0"/"1"` → boolean, decimals), turns empty strings into `NULL`, and upserts (or inserts) in chunks of 500.
- `app/Services/Jdf/JdfBatchImporter.php` — runs a whole batch of files in FK-safe order (per `import_order` in the config), regardless of the order you select/upload them in. Optionally clears every JDF table first.
- `app/Http/Controllers/Admin/JdfImportController.php` — the form (`index`) and the upload handler (`store`).
- `resources/views/admin/jdf_import/index.blade.php` — the panel: current row counts per table, the upload form, and a results table after import.
- `routes/admin.php` — **merge** into your routes (don't overwrite `routes/web.php`).

## ⚠️ Add real auth before deploying this
This panel has **no access control** as shipped — it's wired up as plain routes so you can drop it into whatever admin/auth setup you already have (Breeze, Jetstream, Fortify, a custom guard, spatie/permission, etc.). At minimum, wrap the route group in `routes/admin.php`:

```php
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    // ...
});
```

Anyone who can reach `/admin/jdf-import` can wipe and rewrite your entire JDF dataset via the "replace existing data" option — treat this the same as a database console.

## How matching works
Uploaded filenames are lowercased and stripped of anything that isn't a letter or digit, so `ZasLinky.txt`, `zaslinky.TXT`, and `Zas-Linky.txt` all resolve to the same `zaslinky` key in `config/jdf.php`. Anything that doesn't match a known key is skipped and listed under "neboli rozpoznané" on the results page — nothing fails silently.

## The "replace existing data" checkbox
Four tables have no natural key to upsert on, because the JDF format itself doesn't give them one: `verze_jdf` (batch header), `navaznosti`, `altdop`, `mistenky`. Re-importing without replacing will duplicate rows in those four. For everything else (composite natural keys), upsert means re-running an import is safe and idempotent.

Checking "replace" clears **every** JDF table first (`DELETE`, with `FOREIGN_KEY_CHECKS` off during the clear), regardless of which files are in the current upload. This is intentional: it guarantees a clean, single-batch import every time. The trade-off is that it assumes you're uploading a full batch — if you upload only some of the 17 files with "replace" checked, the tables for the files you left out get wiped and stay empty (nothing repopulates them), which will break any foreign key that pointed into them. Uncheck "replace" for a genuinely partial/incremental upload.

## Known limitation carried over from earlier
Your actual JDF files are version 1.11, and at least `Zasspoje.txt` has more columns (15) than the official 1.10 layout this schema was built from (12). The importer keeps the row and silently truncates extra trailing fields, surfacing a single warning per file rather than one per line — but it does mean a few 1.11-specific fields (whatever they are) aren't captured. If you want those preserved, the fix is to add the extra column(s) to the `zasspoje` migration and to `config/jdf.php`'s column list once their real meaning is confirmed (e.g. from an official 1.11 spec document).

## Try it
1. Copy `config/`, `app/Services/Jdf/`, `app/Http/Controllers/Admin/`, `resources/views/admin/`, and merge `routes/admin.php`.
2. Add auth middleware (see above).
3. Visit `/admin/jdf-import`, select your `.txt` files (multi-select in the file dialog works fine), leave "replace" checked for a full-batch upload, and submit.
