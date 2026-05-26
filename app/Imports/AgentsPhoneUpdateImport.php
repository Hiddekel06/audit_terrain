<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgentsPhoneUpdateImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public int $importedCount = 0;
    public int $updatedCount = 0;
    public array $skippedAgents = [];
    private array $skippedRows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $rowCollection) {
            $arr = array_map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            }, $rowCollection->toArray());

            $normalizeKey = function ($key) {
                $key = mb_strtolower(trim((string) $key), 'UTF-8');
                $key = str_replace(
                    ['à','á','â','ã','ä','å','è','é','ê','ë','ì','í','î','ï','ò','ó','ô','õ','ö','ù','ú','û','ü','ç','ñ','’','\''],
                    ['a','a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n','',''],
                    $key
                );
                $key = preg_replace('/[^a-z0-9]+/u', '_', $key) ?? $key;

                return trim($key, '_');
            };

            $row = [];
            foreach ($arr as $key => $value) {
                $row[$normalizeKey($key)] = $value;
            }

            $lineNumber = is_numeric($index) ? $index + 2 : null;
            $matricule = $this->normalizeMatricule($row['matricule'] ?? $row['cin'] ?? '');
            $telephone = $this->normalizeTelephone($row['telephone'] ?? $row['tel'] ?? $row['numero_telephone'] ?? '');

            if ($matricule === '') {
                $this->skippedAgents[] = 'Ligne ' . ($lineNumber ?? '?') . ' - matricule manquant';
                $this->skippedRows[] = [
                    'line' => $lineNumber,
                    'matricule' => '',
                    'telephone' => $telephone,
                    'issues' => 'Matricule manquant',
                    'warnings' => '',
                    'raw' => json_encode($row, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            if ($telephone === '') {
                $this->skippedAgents[] = 'Ligne ' . ($lineNumber ?? '?') . ' - téléphone manquant pour ' . $matricule;
                $this->skippedRows[] = [
                    'line' => $lineNumber,
                    'matricule' => $matricule,
                    'telephone' => '',
                    'issues' => 'Téléphone manquant',
                    'warnings' => '',
                    'raw' => json_encode($row, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            $user = User::where('matricule', $matricule)->first();

            if (!$user) {
                $this->skippedAgents[] = 'Ligne ' . ($lineNumber ?? '?') . ' - matricule introuvable: ' . $matricule;
                $this->skippedRows[] = [
                    'line' => $lineNumber,
                    'matricule' => $matricule,
                    'telephone' => $telephone,
                    'issues' => 'Matricule introuvable',
                    'warnings' => '',
                    'raw' => json_encode($row, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            if (($user->telephone ?? '') === $telephone) {
                $this->skippedAgents[] = 'Ligne ' . ($lineNumber ?? '?') . ' - téléphone déjà identique pour ' . $matricule;
                $this->skippedRows[] = [
                    'line' => $lineNumber,
                    'matricule' => $matricule,
                    'telephone' => $telephone,
                    'issues' => 'Téléphone déjà identique',
                    'warnings' => '',
                    'raw' => json_encode($row, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            $existingTelephoneUser = User::where('telephone', $telephone)
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingTelephoneUser) {
                $this->skippedAgents[] = 'Ligne ' . ($lineNumber ?? '?') . ' - téléphone déjà utilisé par un autre agent';
                $this->skippedRows[] = [
                    'line' => $lineNumber,
                    'matricule' => $matricule,
                    'telephone' => $telephone,
                    'issues' => 'Téléphone déjà utilisé par un autre agent',
                    'warnings' => '',
                    'raw' => json_encode($row, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            $user->telephone = $telephone;
            $user->save();

            $this->updatedCount++;
            $this->importedCount++;
        }

        if (!empty($this->skippedRows)) {
            try {
                $dir = storage_path('app/imports');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }

                $ts = date('Ymd_His');
                $file = $dir . DIRECTORY_SEPARATOR . "skipped_{$ts}.csv";
                $fp = fopen($file, 'w');
                if ($fp) {
                    fputcsv($fp, ['line', 'matricule', 'telephone', 'issues', 'warnings', 'raw']);
                    foreach ($this->skippedRows as $r) {
                        fputcsv($fp, [
                            $r['line'] ?? '',
                            $r['matricule'] ?? '',
                            $r['telephone'] ?? '',
                            $r['issues'] ?? '',
                            $r['warnings'] ?? '',
                            $r['raw'] ?? '',
                        ]);
                    }
                    fclose($fp);
                    Log::info('AgentsPhoneUpdateImport: skipped rows CSV written', ['file' => $file]);
                }
            } catch (\Throwable $e) {
                Log::error('AgentsPhoneUpdateImport: failed to write skipped CSV', ['error' => $e->getMessage()]);
            }
        }
    }

    private function normalizeMatricule($value): string
    {
        $matricule = strtoupper(trim((string) $value));
        $matricule = preg_replace('/[\s\/]+/', '', $matricule) ?? $matricule;

        if ($matricule === '' || !preg_match('/^[0-9]{6}[A-Z]$/', $matricule)) {
            return '';
        }

        return $matricule;
    }

    private function normalizeTelephone($value): string
    {
        $telephone = preg_replace('/\D+/', '', (string) $value) ?? '';

        if (strlen($telephone) > 9) {
            $telephone = substr($telephone, -9);
        }

        return $telephone;
    }
}