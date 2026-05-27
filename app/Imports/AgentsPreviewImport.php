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

        $normalizeValue = function ($value) {
            if (!is_string($value)) {
                return $value;
            }

            $trimmed = trim($value);
            if ($trimmed === '') {
                return null;
            }

            if (strtolower($trimmed) === 'null') {
                return null;
            }

            return $trimmed;
        };

        $lastStructure = null;
        $lastProfil = null;
        $lastMetier = null;
        $lastDirection = null;

        $this->rows = $rows->map(function ($row) use ($normalizeKey, $normalizeValue, &$lastStructure, &$lastProfil, &$lastMetier, &$lastDirection) {
            $raw = array_map(function ($v) use ($normalizeValue) { return $normalizeValue($v); }, $row->toArray());

            $arr = [];
            foreach ($raw as $k => $v) {
                $arr[$normalizeKey($k)] = $v;
            }

            $structureRaw = $arr['structure'] ?? ($arr['ministere'] ?? null);
            $profilRaw = $arr['profil'] ?? null;
            $metierRaw = $arr['metier'] ?? null;
            $directionRaw = $arr['direction'] ?? null;

            if ($structureRaw !== null) {
                $lastStructure = $structureRaw;
            } else {
                $arr['structure'] = $lastStructure;
                $arr['ministere'] = $lastStructure;
            }

            if ($profilRaw !== null) {
                $lastProfil = $profilRaw;
            } else {
                $arr['profil'] = $lastProfil;
            }

            if ($metierRaw !== null) {
                $lastMetier = $metierRaw;
            } else {
                $arr['metier'] = $lastMetier;
            }

            if ($directionRaw !== null) {
                $lastDirection = $directionRaw;
            } else {
                $arr['direction'] = $lastDirection;
            }

            return $arr;
        })->values()->all();
    }
}
