<?php

namespace App\Services\Jdf;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Imports a single JDF file's raw contents into its target table, per the
 * mapping in config/jdf.php.
 */
class JdfFileImporter
{
    /**
     * @return array{table: string, total: int, imported: int, skipped: int, warnings: array<int, string>}
     */
    public function import(string $key, string $rawContents): array
    {
        $definition = config("jdf.files.{$key}");

        if (! $definition) {
            return [
                'table' => '(neznáme)',
                'total' => 0,
                'imported' => 0,
                'skipped' => 0,
                'warnings' => ["Súbor \"{$key}\" nezodpovedá žiadnej známej JDF tabuľke — preskočené."],
            ];
        }

        $table = $definition['table'];
        $columns = $definition['columns'];
        $casts = $definition['casts'] ?? [];
        $uniqueBy = $definition['unique_by'] ?? null;

        $contents = JdfLineParser::toUtf8($rawContents);
        $lines = preg_split('/\r\n|\r|\n/', $contents);

        $rows = [];
        $warnings = [];
        $total = 0;

        foreach ($lines as $lineNumber => $line) {
            if (trim($line) === '') {
                continue;
            }

            $fields = JdfLineParser::parseLine($line);

            if ($fields === []) {
                continue;
            }

            $total++;

            // Some JDF 1.11 files insert extra columns in the MIDDLE of the row, not
            // just at the end — for `linky` specifically, real-world data has one
            // extra field between platnost_lic_do and platnost_jr_od. Blindly
            // truncating from the end (as below) would silently misalign every field
            // after the insertion point, corrupting values like rozliseni_linky
            // without any error — exactly what caused a real FK failure here. Where
            // the extra field's position is known, drop it precisely instead.
            $dropIndexes = $definition['drop_indexes'] ?? [];

            if ($dropIndexes !== [] && count($fields) === count($columns) + count($dropIndexes)) {
                foreach (array_reverse(array_unique($dropIndexes)) as $index) {
                    if (array_key_exists($index, $fields)) {
                        array_splice($fields, $index, 1);
                    }
                }

                if (! isset($warnings['dropped_known_extra_field'])) {
                    $warnings['dropped_known_extra_field'] = 'Riadky obsahovali známe dodatočné pole novšej verzie JDF na očakávanej pozícii — bolo odstránené pred mapovaním na stĺpce.';
                }
            }

            $expected = count($columns);
            $actual = count($fields);

            if ($actual > $expected) {
                // Newer JDF versions (e.g. 1.11) sometimes add trailing columns beyond
                // the official 1.10 layout this schema is built on — keep the row,
                // drop the extra fields, and flag it once per file rather than per line.
                $fields = array_slice($fields, 0, $expected);

                if (! isset($warnings['extra_fields'])) {
                    $warnings['extra_fields'] = "Riadky majú viac polí ({$actual}) než sa očakávalo ({$expected}) — nadbytočné polia boli ignorované (pravdepodobne novšia verzia JDF). Ak sú dáta po importe nesprávne posunuté, pole môže byť v strede riadku, nie na konci — kontaktujte podporu s ukážkou riadku.";
                }
            } elseif ($actual < $expected) {
                $fields = array_pad($fields, $expected, '');

                if (! isset($warnings['missing_fields'])) {
                    $warnings['missing_fields'] = "Riadky mali menej polí ({$actual}) než sa očakávalo ({$expected}) — chýbajúce polia boli doplnené ako prázdne.";
                }
            }

            $row = array_combine($columns, $fields);

            foreach ($casts as $column => $type) {
                $row[$column] = $this->castValue($row[$column] ?? null, $type);
            }

            foreach ($row as $column => $value) {
                if ($value === '') {
                    $row[$column] = null;
                }
            }

            $rows[] = $row;
        }

        $imported = $this->persist($table, $rows, $uniqueBy);

        return [
            'table' => $table,
            'total' => $total,
            'imported' => $imported,
            'skipped' => $total - $imported,
            'warnings' => array_values($warnings),
        ];
    }

    private function castValue(?string $value, string $type): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return match ($type) {
            'int' => (int) $value,
            // JDF uses "0" as a sentinel for "not assigned" on some optional FK
            // columns (e.g. Spoje.KodSkupinySpoju) — there is no group 0, so treat
            // it as NULL rather than a real foreign key value.
            'zero_as_null' => $value === '0' ? null : (int) $value,
            'decimal' => (float) str_replace(',', '.', $value),
            'bool01' => $value === '1',
            'date_ddmmyyyy' => $this->parseDdmmyyyy($value),
            default => $value,
        };
    }

    private function parseDdmmyyyy(string $value): ?string
    {
        if (! preg_match('/^\d{8}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('dmY', $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function persist(string $table, array $rows, ?array $uniqueBy): int
    {
        if ($rows === []) {
            return 0;
        }

        $imported = 0;

        foreach (array_chunk($rows, 500) as $chunk) {
            if ($uniqueBy) {
                $updateColumns = array_diff(array_keys($chunk[0]), $uniqueBy);
                DB::table($table)->upsert($chunk, $uniqueBy, $updateColumns);
            } else {
                DB::table($table)->insert($chunk);
            }

            $imported += count($chunk);
        }

        return $imported;
    }
}
