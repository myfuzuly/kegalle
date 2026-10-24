<?php

namespace App\Helpers;

class Csv
{
    /**
     * fputcsv for user-supplied data: a cell starting with = + - @ (or tab/CR) is run as a formula
     * by Excel/Sheets, so prefix it with ' to keep it plain text.
     */
    public static function put($handle, array $row): void
    {
        fputcsv($handle, array_map(
            fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v,
            $row
        ));
    }
}
