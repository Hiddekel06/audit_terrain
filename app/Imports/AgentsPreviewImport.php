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
        $lastStructure = null;
        $lastProfil = null;
        $lastMetier = null;
        $lastTelephone = null;
        $lastDirection = null;

        $this->rows = $rows->map(function ($row) use (&$lastStructure, &$lastProfil, &$lastMetier, &$lastTelephone, &$lastDirection) {
            $raw = array_map(function ($v) { return is_string($v) ? trim($v) : $v; }, $row->toArray());

            // Normalize header keys to simple ascii lowercase names (e.g. 'Téléphone' -> 'telephone')
            $normalizeKey = function ($k) {
                $k = mb_strtolower(trim((string)$k), 'UTF-8');
                $k = str_replace(
                    ['à','á','â','ã','ä','å','è','é','ê','ë','ì','í','î','ï','ò','ó','ô','õ','ö','ù','ú','û','ü','ç','ñ','’','\''],
                    ['a','a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n','',''],
                    $k
                );
                $k = preg_replace('/[^a-z0-9]+/u', '_', $k) ?? $k;
                return trim($k, '_');
            };

            $arr = [];
            foreach ($raw as $k => $v) {
                $arr[$normalizeKey($k)] = $v;
            }

            // Normalize explicit 'null' token -> treat as explicit empty
            $structureRaw = isset($arr['structure']) ? $arr['structure'] : ($arr['ministere'] ?? null);
            $profilRaw = $arr['profil'] ?? null;
            $metierRaw = $arr['metier'] ?? null;
            $telephoneRaw = $arr['telephone'] ?? ($arr['tel'] ?? ($arr['telephone_mobile'] ?? ($arr['telephone_mobilee'] ?? null)));
            $directionRaw = $arr['direction'] ?? ($arr['direction_service'] ?? null);

            $structureVal = is_string($structureRaw) && strtolower($structureRaw) === 'null' ? null : ($structureRaw === '' ? null : $structureRaw);
            $profilVal = is_string($profilRaw) && strtolower($profilRaw) === 'null' ? null : ($profilRaw === '' ? null : $profilRaw);
            $metierVal = is_string($metierRaw) && strtolower($metierRaw) === 'null' ? null : ($metierRaw === '' ? null : $metierRaw);
            $telephoneVal = is_string($telephoneRaw) && strtolower($telephoneRaw) === 'null' ? null : ($telephoneRaw === '' ? null : $telephoneRaw);
            $directionVal = is_string($directionRaw) && strtolower($directionRaw) === 'null' ? null : ($directionRaw === '' ? null : $directionRaw);

            // Inherit when cell is empty (null) — keep last non-null value
            if ($structureVal !== null) {
                $lastStructure = $structureVal;
            }
            if ($profilVal !== null) {
                $lastProfil = $profilVal;
            }
            if ($metierVal !== null) {
                $lastMetier = $metierVal;
            }
            if ($telephoneVal !== null) {
                $lastTelephone = $telephoneVal;
            }
            if ($directionVal !== null) {
                $lastDirection = $directionVal;
            }

            // Build effective row with inherited values
            $arr['structure_effective'] = $lastStructure;
            $arr['profil_effective'] = $lastProfil;
            $arr['metier_effective'] = $lastMetier;
            // also propagate telephone and direction so parser sees inherited values
            $arr['telephone'] = $lastTelephone;
            $arr['direction'] = $lastDirection;

            return $arr;
        })->values()->all();
    }
}
