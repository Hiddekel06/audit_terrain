<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgentsPreviewImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public array $rows = [];

    public function collection(Collection $rows)
    {
        $normalizeKey = function ($k) {
            $k = mb_strtolower(trim((string) $k), 'UTF-8');
            $k = str_replace(
                ['à','á','â','ã','ä','å','è','é','ê','ë','ì','í','î','ï','ò','ó','ô','õ','ö','ù','ú','û','ü','ç','ñ','’','\''],
                ['a','a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n','',''],
                $k
            );
            $k = preg_replace('/[^a-z0-9]+/u', '_', $k) ?? $k;
            return trim($k, '_');
        };

        $this->rows = $rows->map(function ($row) use ($normalizeKey) {
            $raw = array_map(function ($v) { return is_string($v) ? trim($v) : $v; }, $row->toArray());

            $arr = [];
            foreach ($raw as $k => $v) {
                $arr[$normalizeKey($k)] = $v;
            }

            return $arr;
        })->values()->all();
    }
}
