<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Jdf\JdfBatchImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JdfImportController extends Controller
{
    public function index(): View
    {
        $tableCounts = collect(config('jdf.files'))
            ->map(fn ($def, $key) => [
                'key' => $key,
                'table' => $def['table'],
                'count' => DB::table($def['table'])->count(),
            ])
            ->values();

        return view('admin.jdf_import.index', [
            'tableCounts' => $tableCounts,
            'results' => session('jdf_import_results'),
            'unmatched' => session('jdf_import_unmatched', []),
        ]);
    }

    public function store(Request $request, JdfBatchImporter $importer): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:20480'], // 20 MB per file
            'replace' => ['nullable', 'boolean'],
        ]);

        $known = array_keys(config('jdf.files'));
        $filesByKey = [];
        $unmatched = [];

        foreach ($request->file('files') as $uploaded) {
            $key = $this->normalizeFilename($uploaded->getClientOriginalName());

            if (! in_array($key, $known, true)) {
                $unmatched[] = $uploaded->getClientOriginalName();

                continue;
            }

            $filesByKey[$key] = file_get_contents($uploaded->getRealPath());
        }

        $results = $importer->run($filesByKey, (bool) $request->boolean('replace'));

        return redirect()
            ->route('admin.jdf-import.index')
            ->with('jdf_import_results', $results)
            ->with('jdf_import_unmatched', $unmatched);
    }

    /** "ZasLinky.txt" / "zaslinky.TXT" / "Zas Linky.txt" -> "zaslinky" */
    private function normalizeFilename(string $filename): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);

        return strtolower(preg_replace('/[^A-Za-z0-9]/', '', $base));
    }
}
