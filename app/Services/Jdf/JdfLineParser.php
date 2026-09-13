<?php

namespace App\Services\Jdf;

/**
 * Parses one line of a raw JDF file, e.g.:
 *   "207101","1","1","63524","","","","","46266";
 * into ["207101","1","1","63524","","","","","46266"].
 */
class JdfLineParser
{
    /** @return array<int, string> */
    public static function parseLine(string $line): array
    {
        $line = rtrim($line, "\r\n");
        $line = rtrim($line);
        $line = rtrim($line, ';'); // JDF terminates every row with a semicolon

        if ($line === '') {
            return [];
        }

        $fields = str_getcsv($line, ',', '"');

        return array_map(fn ($field) => trim((string) $field), $fields);
    }

    /**
     * JDF's native encoding is Windows-1250 (CP1250), but real-world exports are
     * inconsistent — some files in the same batch have shown up as UTF-16 (common
     * from certain Windows tools), which CP1250 conversion would badly mangle
     * (UTF-16's embedded null bytes break comma/quote parsing). Detect known BOMs
     * first and only fall back to "assume CP1250" for plain byte-oriented text.
     */
    public static function toUtf8(string $contents): string
    {
        // UTF-16 with BOM — strip the BOM and convert properly instead of treating
        // the two-bytes-per-character content as single-byte CP1250.
        if (str_starts_with($contents, "\xFF\xFE")) {
            return self::convertOrFail($contents, 'UTF-16LE');
        }

        if (str_starts_with($contents, "\xFE\xFF")) {
            return self::convertOrFail($contents, 'UTF-16BE');
        }

        // UTF-8 BOM — strip it, the rest is already valid UTF-8.
        if (str_starts_with($contents, "\xEF\xBB\xBF")) {
            return substr($contents, 3);
        }

        if (mb_check_encoding($contents, 'UTF-8')) {
            return $contents;
        }

        // Heuristic: UTF-16 without a BOM still has a very distinctive pattern for
        // Latin-script text — every other byte is 0x00. CP1250 files (and any other
        // single-byte encoding) essentially never contain null bytes at all.
        if (str_contains(substr($contents, 0, 200), "\x00")) {
            $isLikelyUtf16Le = substr($contents, 1, 1) === "\x00";

            return self::convertOrFail($contents, $isLikelyUtf16Le ? 'UTF-16LE' : 'UTF-16BE');
        }

        return self::convertOrFail($contents, 'Windows-1250');
    }

    private static function convertOrFail(string $contents, string $fromEncoding): string
    {
        // Prefer iconv: it's a separate, near-universally-available extension with
        // full support for all of the above encodings. Some environments run
        // Symfony's mbstring polyfill instead of the real ext-mbstring, which only
        // understands a handful of encodings and throws a ValueError otherwise.
        if (function_exists('iconv')) {
            $converted = @iconv($fromEncoding, 'UTF-8//TRANSLIT//IGNORE', $contents);

            if ($converted !== false) {
                return $converted;
            }
        }

        try {
            return mb_convert_encoding($contents, 'UTF-8', $fromEncoding);
        } catch (\ValueError) {
            // Nothing available supports this encoding. Strip invalid byte sequences
            // so the import doesn't crash — some characters may be lost, but the row
            // structure (numbers, codes, dates) is what matters most for the FKs.
            return mb_convert_encoding($contents, 'UTF-8', 'UTF-8');
        }
    }
}
