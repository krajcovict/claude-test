<?php

namespace App\Services\Jdf;

use Illuminate\Support\Facades\DB;

/**
 * Runs a batch of uploaded JDF files through JdfFileImporter, in the
 * dependency-safe order defined in config/jdf.php, optionally clearing every
 * JDF table first.
 */
class JdfBatchImporter
{
    public function __construct(private JdfFileImporter $fileImporter)
    {
    }

    /**
     * @param  array<string, string>  $filesByKey  jdf-file-key => raw file contents
     * @return array<int, array{key: string, table: string, total: int, imported: int, skipped: int, warnings: array<int, string>}>
     */
    public function run(array $filesByKey, bool $replaceExisting): array
    {
        if ($replaceExisting) {
            $this->clearAllTables();
        }

        $order = config('jdf.import_order');
        $results = [];

        // Import in FK-safe order regardless of the order files were uploaded in.
        foreach ($order as $key) {
            if (! array_key_exists($key, $filesByKey)) {
                continue;
            }

            $results[] = ['key' => $key] + DB::transaction(
                fn () => $this->fileImporter->import($key, $filesByKey[$key])
            );
        }

        return $results;
    }

    /**
     * Deletes every row from every JDF table, in reverse dependency order
     * (children first). Assumes a full batch is being (re-)uploaded — if
     * you're only uploading some of the 17 files, leave "replace" unchecked
     * or the tables for the files you didn't include will be left empty.
     */
    private function clearAllTables(): void
    {
        $tables = collect(config('jdf.files'))->pluck('table')->reverse()->values();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            DB::table($table)->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
