<?php

/**
 * One entry per JDF file. `columns` lists the fields in the exact order the
 * JDF 1.10 spec (and the migrations built from it) define them — this is
 * what maps each CSV position to a database column.
 *
 * `unique_by` gives the natural-key columns to upsert on (re-uploading the
 * same batch updates existing rows instead of duplicating them). Tables
 * without a natural key in the format (Navaznosti, Altdop, Mistenky, and the
 * batch-header VerzeJdf) are plain-inserted — use the "replace existing
 * data" option on the import form to avoid piling up duplicates on repeat
 * uploads of those.
 *
 * `casts` flags columns that need converting from the raw JDF text
 * representation: JDF dates are "DDMMYYYY" strings, booleans are "0"/"1".
 */
return [

    'import_order' => [
        'verzejdf', 'pevnykod', 'zastavky', 'oznacniky', 'dopravci', 'linky',
        'linext', 'spojskup', 'spoje', 'zaslinky', 'zasspoje', 'udaje',
        'caskody', 'navaznosti', 'altdop', 'altlinky', 'mistenky',
    ],

    'files' => [

        'verzejdf' => [
            'table' => 'verze_jdf',
            'columns' => ['cislo_verze_jdf', 'cislo_du', 'okres_kraj', 'identifikace_davky', 'datum_vyroby_davky', 'jmeno'],
            'casts' => ['datum_vyroby_davky' => 'date_ddmmyyyy'],
            'unique_by' => null,
        ],

        'pevnykod' => [
            'table' => 'pevnykod',
            'columns' => ['cislo_pevneho_kodu', 'oznaceni_pevneho_kodu', 'rezerva'],
            'casts' => [],
            'unique_by' => ['cislo_pevneho_kodu'],
        ],

        'zastavky' => [
            'table' => 'zastavky',
            'columns' => [
                'cislo_zastavky', 'nazev_obce', 'cast_obce', 'blizsi_misto', 'blizka_obec', 'stat',
                'pev_kod_1', 'pev_kod_2', 'pev_kod_3', 'pev_kod_4', 'pev_kod_5', 'pev_kod_6',
            ],
            'casts' => ['cislo_zastavky' => 'int'],
            'unique_by' => ['cislo_zastavky'],
        ],

        'oznacniky' => [
            'table' => 'oznacniky',
            'columns' => ['cislo_zastavky', 'kod_oznacniku', 'nazev', 'smer_popis', 'stanoviste', 'rezerva1', 'rezerva2'],
            'casts' => ['cislo_zastavky' => 'int', 'kod_oznacniku' => 'int'],
            'unique_by' => ['cislo_zastavky', 'kod_oznacniku'],
        ],

        'dopravci' => [
            'table' => 'dopravci',
            'columns' => [
                'ic', 'dic', 'obchodni_jmeno', 'druh_firmy', 'jmeno_fyz_osoby', 'sidlo',
                'telefon_sidla', 'telefon_dispecink', 'telefon_informace', 'fax', 'email', 'www',
                'rozliseni_dopravce',
            ],
            'casts' => ['druh_firmy' => 'int', 'rozliseni_dopravce' => 'int'],
            'unique_by' => ['ic', 'rozliseni_dopravce'],
        ],

        'linky' => [
            'table' => 'linky',
            'columns' => [
                'cislo_linky', 'nazev_linky', 'ic_dopravce', 'typ_linky', 'dopravni_prostredek',
                'objizdkovy_jr', 'seskupeni_spoju', 'pouziti_oznacniku', 'rezerva', 'cislo_licence',
                'platnost_lic_od', 'platnost_lic_do', 'platnost_jr_od', 'platnost_jr_do',
                'rozliseni_dopravce', 'rozliseni_linky',
            ],
            // Real-world JDF 1.11 Linky.txt has one extra (unidentified, always empty
            // in samples seen so far) field between platnost_lic_do and platnost_jr_od
            // — 0-indexed position 12. Without dropping it precisely, every field after
            // it shifts by one, corrupting rozliseni_dopravce/rozliseni_linky silently.
            'drop_indexes' => [12],
            'casts' => [
                'cislo_linky' => 'int', 'rozliseni_dopravce' => 'int', 'rozliseni_linky' => 'int',
                'objizdkovy_jr' => 'bool01', 'seskupeni_spoju' => 'bool01', 'pouziti_oznacniku' => 'bool01',
                'platnost_lic_od' => 'date_ddmmyyyy', 'platnost_lic_do' => 'date_ddmmyyyy',
                'platnost_jr_od' => 'date_ddmmyyyy', 'platnost_jr_do' => 'date_ddmmyyyy',
            ],
            'unique_by' => ['cislo_linky', 'rozliseni_linky'],
        ],

        'linext' => [
            'table' => 'lin_ext',
            'columns' => ['cislo_linky', 'poradi', 'kod_dopravy', 'oznaceni_linky', 'preference_oznaceni', 'rezerva', 'rozliseni_linky'],
            'casts' => ['cislo_linky' => 'int', 'poradi' => 'int', 'kod_dopravy' => 'int', 'rozliseni_linky' => 'int', 'preference_oznaceni' => 'bool01'],
            'unique_by' => ['cislo_linky', 'rozliseni_linky', 'poradi'],
        ],

        'spojskup' => [
            'table' => 'spoj_skup',
            'columns' => ['kod_skupiny_spoju', 'poradi', 'nazev', 'popis', 'rezerva'],
            'casts' => ['kod_skupiny_spoju' => 'int', 'poradi' => 'int'],
            'unique_by' => ['kod_skupiny_spoju'],
        ],

        'spoje' => [
            'table' => 'spoje',
            'columns' => array_merge(
                ['cislo_linky', 'cislo_spoje'],
                array_map(fn ($n) => "pev_kod_{$n}", range(1, 10)),
                ['kod_skupiny_spoju', 'rozliseni_linky'],
            ),
            'casts' => ['cislo_linky' => 'int', 'cislo_spoje' => 'int', 'kod_skupiny_spoju' => 'zero_as_null', 'rozliseni_linky' => 'int'],
            'unique_by' => ['cislo_linky', 'rozliseni_linky', 'cislo_spoje'],
        ],

        'zaslinky' => [
            'table' => 'zaslinky',
            'columns' => [
                'cislo_linky', 'cislo_tarifni', 'tarifni_pasmo', 'cislo_zastavky', 'prumerna_doba',
                'pev_kod_1', 'pev_kod_2', 'pev_kod_3', 'rozliseni_linky',
            ],
            'casts' => ['cislo_linky' => 'int', 'cislo_tarifni' => 'int', 'cislo_zastavky' => 'int', 'rozliseni_linky' => 'int'],
            'unique_by' => ['cislo_linky', 'rozliseni_linky', 'cislo_tarifni'],
        ],

        'zasspoje' => [
            'table' => 'zasspoje',
            'columns' => [
                'cislo_linky', 'cislo_spoje', 'cislo_tarifni', 'cislo_zastavky', 'kod_oznacniku',
                'cislo_stanoviste', 'pev_kod_1', 'pev_kod_2', 'kilometry', 'cas_prijezdu', 'cas_odjezdu',
                'rozliseni_linky',
            ],
            // Confirmed against real ZasSpoje.txt (15 fields/row): one unidentified,
            // always-empty extra field sits between pev_kod_2 and kilometry (0-indexed
            // position 8), and two more trail after cas_odjezdu, before rozliseni_linky
            // (positions 12, 13). Dropping only the end fields (as a naive fix would)
            // corrupts everything from kilometry onward — confirmed by cross-checking
            // real rows against the matching ZasLinky.txt tariff sequence and its
            // known departure-time progression.
            'drop_indexes' => [8, 12, 13],
            'casts' => [
                'cislo_linky' => 'int', 'cislo_spoje' => 'int', 'cislo_tarifni' => 'int',
                'cislo_zastavky' => 'int', 'kod_oznacniku' => 'int', 'rozliseni_linky' => 'int',
                'kilometry' => 'decimal',
            ],
            'unique_by' => ['cislo_linky', 'rozliseni_linky', 'cislo_spoje', 'cislo_tarifni'],
        ],

        'udaje' => [
            'table' => 'udaje',
            'columns' => ['cislo_linky', 'cislo_udaje', 'text', 'rozliseni_linky'],
            'casts' => ['cislo_linky' => 'int', 'cislo_udaje' => 'int', 'rozliseni_linky' => 'int'],
            'unique_by' => ['cislo_linky', 'rozliseni_linky', 'cislo_udaje'],
        ],

        'caskody' => [
            'table' => 'caskody',
            'columns' => [
                'cislo_linky', 'cislo_spoje', 'cislo_casoveho_kodu', 'oznaceni_casoveho_kodu',
                'typ_casove_kodu', 'datum_od', 'datum_do', 'poznamka', 'rozliseni_linky',
            ],
            'casts' => [
                'cislo_linky' => 'int', 'cislo_spoje' => 'int', 'cislo_casoveho_kodu' => 'int', 'rozliseni_linky' => 'int',
                'datum_od' => 'date_ddmmyyyy', 'datum_do' => 'date_ddmmyyyy',
            ],
            'unique_by' => ['cislo_linky', 'rozliseni_linky', 'cislo_spoje', 'cislo_casoveho_kodu'],
        ],

        'navaznosti' => [
            'table' => 'navaznosti',
            'columns' => [
                'typ_navaznosti', 'cislo_linky', 'cislo_spoje', 'cislo_tarifni', 'cislo_prestupni_linky',
                'cislo_zastavky_prestupni_linky', 'kod_oznacniku_prestupni_linky',
                'cislo_vych_konc_zast_prestupni', 'kod_vych_konc_oznac_prestupni', 'doba_cekani',
                'rozliseni_linky',
            ],
            'casts' => [
                'cislo_linky' => 'int', 'cislo_spoje' => 'int', 'cislo_tarifni' => 'int',
                'cislo_prestupni_linky' => 'int', 'cislo_zastavky_prestupni_linky' => 'int',
                'kod_oznacniku_prestupni_linky' => 'int', 'cislo_vych_konc_zast_prestupni' => 'int',
                'kod_vych_konc_oznac_prestupni' => 'int', 'doba_cekani' => 'int', 'rozliseni_linky' => 'int',
            ],
            'unique_by' => null,
        ],

        'altdop' => [
            'table' => 'altdop',
            'columns' => array_merge(
                ['cislo_linky', 'cislo_spoje', 'ic_dopravce'],
                array_map(fn ($n) => "pev_kod_{$n}", range(1, 6)),
                ['typ_casoveho_kodu', 'rezerva', 'datum_od', 'datum_do', 'rozliseni_dopravce', 'rozliseni_linky'],
            ),
            'casts' => [
                'cislo_linky' => 'int', 'cislo_spoje' => 'int', 'rozliseni_dopravce' => 'int', 'rozliseni_linky' => 'int',
                'datum_od' => 'date_ddmmyyyy', 'datum_do' => 'date_ddmmyyyy',
            ],
            'unique_by' => null,
        ],

        'altlinky' => [
            'table' => 'altlinky',
            'columns' => ['cislo_linky', 'alt_cislo_linky', 'stat', 'rozliseni_linky'],
            'casts' => ['cislo_linky' => 'int', 'rozliseni_linky' => 'int'],
            'unique_by' => ['cislo_linky', 'rozliseni_linky', 'alt_cislo_linky', 'stat'],
        ],

        'mistenky' => [
            'table' => 'mistenky',
            'columns' => ['cislo_linky', 'cislo_spoje', 'text_informace', 'rozliseni_linky'],
            'casts' => ['cislo_linky' => 'int', 'cislo_spoje' => 'int', 'rozliseni_linky' => 'int'],
            'unique_by' => null,
        ],
    ],
];
