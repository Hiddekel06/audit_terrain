<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Profil;
use App\Models\Ministere;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class AgentsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $profils;
    private $ministeres;
    private array $ministereAliases = [];
    private array $profilAliases = [];
    public $importedCount = 0;
    public $skippedAgents = [];
    private $skippedRows = [];

    public function __construct()
    {
        $this->profils = Profil::all()->pluck('id', 'libelle')->mapWithKeys(function ($id, $libelle) {
            return [strtolower(trim($libelle)) => $id];
        });
        
        $this->ministeres = Ministere::all()->pluck('id', 'nom')->mapWithKeys(function ($id, $nom) {
            return [strtolower(trim($nom)) => $id];
        });

        $this->ministereAliases = [
            'ministere en charge de la sante' => 'sante',
            'ministere en charge de la famille' => 'famille',
            'ministere en charge de la jeunesse' => 'jeunesse',
            'ministere en charge des affaires etrangeres' => 'affaires etrangeres',
            'ministere de l education nationale' => 'Ministère de l’Enseignement supérieur, de la Recherche et de l’Innovation',
            'ministere des finances et du budget' => 'finances budget',
            'ministere des infrastructures' => 'infrastructures',
            'ministere de l interieur et de la securite publique' => 'interieur securite publique',
            'ministere de la justice' => 'justice',
            'ministere en charge de la formation professionnelles' => 'formation professionnelle',
            'ministere en charge de l energie' => 'energie',
            'ministere en charge de l economie' => 'economie',
            'ministere en charge de l urbanisme' => 'urbanisme',
            'ministere en charge de la microfinance' => 'microfinance',
            'ministere en charge des transports' => 'transports',
        ];

        $this->profilAliases = [
            "chef d'equipe" => "chef d'équipe",
            'chefs d equipes' => "chef d'équipe",
            'chefs d equipe' => "chef d'équipe",
            'cheffe d equipe' => "chef d'équipe",
            'cheffe d\'equipe' => "chef d'équipe",
            'chef d\'équipe' => "chef d'équipe",
            'agents de support technique' => 'agent de support technique',
            'agent de support technique' => 'agent de support technique',
            'agent de support' => 'agent de support technique',
            'agents de  de support technique' => 'agent de support technique',
            'agent d appui' => 'agent de support technique',
            'agent d\'appui' => 'agent de support technique',
            'agent auditeur' => 'agent auditeur',
            'agents auditeurs' => 'agent auditeur',
            'auditeurs' => 'agent auditeur',
            'auditeurs ou agents de support technique' => 'agent de support technique',
            'superviseurs' => 'superviseur',
            'superviseur' => 'superviseur',
            'chauffeurs' => 'chauffeur',
        ];
    }

    public function collection(Collection $rows)
    {
        $lastStructure = null;
        $lastProfil = null;
        $lastMetier = null;

        foreach ($rows as $index => $rowCollection) {
            $arr = array_map(function ($v) { return is_string($v) ? trim($v) : $v; }, $rowCollection->toArray());

            $structureRaw = isset($arr['structure']) ? $arr['structure'] : ($arr['ministere'] ?? null);
            $profilRaw = $arr['profil'] ?? null;
            $metierRaw = $arr['metier'] ?? null;

            $structureNormalized = is_string($structureRaw) ? strtolower(trim($structureRaw)) : null;
            $profilNormalized = is_string($profilRaw) ? strtolower(trim($profilRaw)) : null;
            $metierNormalized = is_string($metierRaw) ? strtolower(trim($metierRaw)) : null;

            // 'null' token means explicit empty -> unset last value and effective is null
            if ($structureNormalized === 'null') {
                $lastStructure = null;
                $effectiveStructure = null;
            } elseif ($structureRaw === '' || $structureRaw === null) {
                $effectiveStructure = $lastStructure;
            } else {
                $lastStructure = $structureRaw;
                $effectiveStructure = $structureRaw;
            }

            if ($profilNormalized === 'null') {
                $lastProfil = null;
                $effectiveProfil = null;
            } elseif ($profilRaw === '' || $profilRaw === null) {
                $effectiveProfil = $lastProfil;
            } else {
                $lastProfil = $profilRaw;
                $effectiveProfil = $profilRaw;
            }

            if ($metierNormalized === 'null') {
                $lastMetier = null;
                $effectiveMetier = null;
            } elseif ($metierRaw === '' || $metierRaw === null) {
                $effectiveMetier = $lastMetier;
            } else {
                $lastMetier = $metierRaw;
                $effectiveMetier = $metierRaw;
            }

            // Merge effective values into row for parsing
            $effectiveRow = $arr;
            $effectiveRow['structure'] = $effectiveStructure;
            $effectiveRow['ministere'] = $effectiveStructure;
            $effectiveRow['profil'] = $effectiveProfil;
            $effectiveRow['metier'] = $effectiveMetier;

            $parsed = $this->parseRow($effectiveRow, is_numeric($index) ? $index + 2 : null);

            if (!$parsed['can_import']) {
                $this->skippedAgents[] = $parsed['display_name'] . ' (' . ($parsed['matricule'] ?: 'sans matricule') . ') - ' . implode('; ', $parsed['issues']);
                $this->skippedRows[] = [
                    'line' => $parsed['line'] ?? null,
                    'display_name' => $parsed['display_name'] ?? '',
                    'matricule' => $parsed['matricule'] ?: '',
                    'structure' => $arr['structure'] ?? ($arr['ministere'] ?? ''),
                    'profil' => $arr['profil'] ?? '',
                    'telephone' => $arr['telephone'] ?? '',
                    'issues' => implode('; ', $parsed['issues'] ?? []),
                    'warnings' => implode('; ', $parsed['warnings'] ?? []),
                    'raw' => json_encode($arr, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            // Determine matricule to store: prefer provided matricule, else use telephone if present.
            $matriculeToStore = $parsed['matricule'] ?: null;
            if (empty($matriculeToStore)) {
                // normalize telephone to digits only for use as matricule
                $tel = preg_replace('/[^0-9]+/', '', $parsed['telephone'] ?? '');
                $matriculeToStore = $tel ?: null;
            }

            // If still empty, skip this row (no matricule and no telephone)
            if (empty($matriculeToStore)) {
                $this->skippedAgents[] = $parsed['display_name'] . ' (' . ($parsed['matricule'] ?: 'sans matricule') . ') - Matricule et téléphone manquants';
                $this->skippedRows[] = [
                    'line' => $parsed['line'] ?? null,
                    'display_name' => $parsed['display_name'] ?? '',
                    'matricule' => $parsed['matricule'] ?: '',
                    'structure' => $arr['structure'] ?? ($arr['ministere'] ?? ''),
                    'profil' => $arr['profil'] ?? '',
                    'telephone' => $arr['telephone'] ?? '',
                    'issues' => 'Matricule et téléphone manquants',
                    'warnings' => implode('; ', $parsed['warnings'] ?? []),
                    'raw' => json_encode($arr, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            // Check duplicate on resulting matricule (whether original or telephone used)
            if (User::where('matricule', $matriculeToStore)->exists()) {
                $this->skippedAgents[] = $parsed['display_name'] . ' (' . $matriculeToStore . ') - Doublon matricule/téléphone';
                $this->skippedRows[] = [
                    'line' => $parsed['line'] ?? null,
                    'display_name' => $parsed['display_name'] ?? '',
                    'matricule' => $matriculeToStore,
                    'structure' => $arr['structure'] ?? ($arr['ministere'] ?? ''),
                    'profil' => $arr['profil'] ?? '',
                    'telephone' => $arr['telephone'] ?? '',
                    'issues' => 'Doublon matricule',
                    'warnings' => implode('; ', $parsed['warnings'] ?? []),
                    'raw' => json_encode($arr, JSON_UNESCAPED_UNICODE),
                ];
                continue;
            }

            User::create([
                'prenom' => $parsed['prenom'],
                'nom' => $parsed['nom'],
                'matricule' => $matriculeToStore,
                'telephone' => $parsed['telephone'],
                'email' => $parsed['email'],
                'ministere_id' => $parsed['ministere_id'],
                'direction' => $parsed['direction'],
                'metier' => $parsed['metier'],
                'source_type' => 'import',
                'profil_id' => $parsed['profil_id'],
                'profil_initial_id' => $parsed['profil_id'],
                'profil_secondaires' => $parsed['profil_secondaires'] ?? [],
                'niveau_numerique' => null,
                'experiences' => [],
                'competences_techniques' => [],
                'ready_to_deploy_all_regions' => true,
                'disponibilite' => 'immediate',
            ]);

            $this->importedCount++;
        }

        // After processing all rows, if there are skipped rows, dump them to CSV for review
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
                    // header
                    fputcsv($fp, ['line','display_name','matricule','structure','profil','telephone','issues','warnings','raw']);
                    foreach ($this->skippedRows as $r) {
                        fputcsv($fp, [
                            $r['line'] ?? '',
                            $r['display_name'] ?? '',
                            $r['matricule'] ?? '',
                            $r['structure'] ?? '',
                            $r['profil'] ?? '',
                            $r['telephone'] ?? '',
                            $r['issues'] ?? '',
                            $r['warnings'] ?? '',
                            $r['raw'] ?? '',
                        ]);
                    }
                    fclose($fp);
                    Log::info('AgentsImport: skipped rows CSV written', ['file' => $file]);
                }
            } catch (\Throwable $e) {
                Log::error('AgentsImport: failed to write skipped CSV', ['error' => $e->getMessage()]);
            }
        }
    }

    public function parseRow(array $row, ?int $lineNumber = null): array
    {
        $normalize = function ($v) {
            $s = is_string($v) ? trim($v) : trim((string)$v);
            $low = strtolower($s);
            $emptyTokens = ['null', 'n/a', 'na', '-', '—', '–', 'aucun', 'sans', 'none', ''];
            if (in_array($low, $emptyTokens, true)) {
                return '';
            }
            return $s;
        };

        $prenom = $normalize($row['prenom'] ?? '');
        $nom = $normalize($row['nom'] ?? '');
        $fullName = $normalize($row['membres'] ?? $row['nom_complet'] ?? $row['membre'] ?? '');

        if (($prenom === '' || $nom === '') && $fullName !== '') {
            [$prenomFromFullName, $nomFromFullName] = $this->splitFullName($fullName);
            if ($prenom === '') {
                $prenom = $prenomFromFullName;
            }
            if ($nom === '') {
                $nom = $nomFromFullName;
            }
        }

        $matriculeRaw = $row['matricule'] ?? $row['cin'] ?? null;
        $matriculeNormToken = $normalize($matriculeRaw);
        if ($matriculeNormToken === '') {
            $matricule = '';
        } else {
            $matricule = $this->normalizeMatricule($matriculeRaw);
        }
        $telephone = $normalize($row['telephone'] ?? '');
        $email = $normalize($row['email'] ?? '');
        $direction = $normalize($row['direction'] ?? 'N/A');
        $metier = $normalize($row['metier'] ?? $row['fonction'] ?? $row['profession'] ?? '');
        $profilInput = strtolower($normalize($row['profil_effective'] ?? $row['profil'] ?? ''));
        $origProfilText = $profilInput;
        $profilAlias = $this->normalizeProfilAlias($profilInput);
        if (!empty($profilAlias)) {
            $profilInput = $profilAlias;
        }

        // If the original input mentions both 'auditeur' and 'support', prefer support IT and keep auditeur as secondary
        $profilSecondaires = [];
        if (str_contains($origProfilText, 'auditeur') && str_contains($origProfilText, 'support')) {
            $profilInput = 'agent de support technique';
            $secondaryId = $this->findBestMatch($this->profils, 'agent auditeur');
            if ($secondaryId) {
                $profilSecondaires[] = $secondaryId;
            }
        }
        $ministereInput = strtolower($normalize($row['structure_effective'] ?? $row['ministere_effective'] ?? $row['structure'] ?? $row['ministere'] ?? ''));

        $issues = [];
        $warnings = [];

        if ($fullName !== '' && $prenom !== '' && $nom === '') {
            $warnings[] = 'Nom de famille absent après découpage du champ Membres';
        }

        if ($prenom === '' && $nom === '' && $fullName === '') {
            $issues[] = 'Champ Membres ou prénom/nom manquant';
        }

        if ($matricule === '') {
            $warnings[] = 'Matricule manquant';
        }

        $profilId = $this->findBestMatch($this->profils, $profilInput) ?: $this->profils->first();
        // if not already filled, attempt to fill secondary from simple mentions like 'auditeur' or 'auditeurs'
        if (empty($profilSecondaires) && str_contains($origProfilText, 'auditeur') && $profilId && $this->normalizeString($this->profils->search($profilId) ?: '') !== 'agentauditeur') {
            $maybeSecondary = $this->findBestMatch($this->profils, 'agent auditeur');
            if ($maybeSecondary && $maybeSecondary !== $profilId) {
                $profilSecondaires[] = $maybeSecondary;
            }
        }
        $ministereId = $this->findBestMatch($this->ministeres, $ministereInput);

        if (!$ministereId) {
            $structureLabel = $row['structure'] ?? $row['ministere'] ?? '—';
            $issues[] = "Structure '{$structureLabel}' inconnue";
        }

        if ($matricule !== '' && User::where('matricule', $matricule)->exists()) {
            $issues[] = 'Doublon matricule';
        }

        if ($prenom === '' || $nom === '') {
            $warnings[] = 'Découpage incomplet du nom';
        }

        $canImport = count($issues) === 0;
        $status = $canImport ? (count($warnings) > 0 ? 'warning' : 'ok') : 'error';

        return [
            'line' => $lineNumber,
            'display_name' => trim($prenom . ' ' . $nom) !== '' ? trim($prenom . ' ' . $nom) : ($fullName !== '' ? $fullName : '—'),
            'full_name' => $fullName,
            'prenom' => $prenom,
            'nom' => $nom,
            'matricule' => $matricule,
            'telephone' => $telephone,
            'email' => $email,
            'direction' => $direction,
            'metier' => $metier,
            'profil_input' => $profilInput,
            'ministere_input' => $ministereInput,
            'profil_id' => $profilId,
            'profil_secondaires' => $profilSecondaires,
            'ministere_id' => $ministereId,
            'warnings' => $warnings,
            'issues' => $issues,
            'status' => $status,
            'can_import' => $canImport,
        ];
    }

    public function splitFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];

        if (count($parts) <= 1) {
            $single = trim($fullName);
            return [$single, ''];
        }

        $nom = array_pop($parts);
        $prenom = implode(' ', $parts);

        return [trim($prenom), trim($nom)];
    }

    private function normalizeMatricule($value): string
    {
        $matricule = strtoupper(trim((string) $value));
        $matricule = preg_replace('/[\s\/]+/', '', $matricule) ?? $matricule;

        return $matricule;
    }

    /**
     * Tente de trouver la meilleure correspondance (insensible aux accents, recherche partielle).
     */
    private function findBestMatch($collection, $input)
    {
        if (empty($input)) return null;

        $inputNormalized = $this->normalizeString($input);
        $inputAlias = $this->normalizeMinistereAlias($input);
        $inputTokens = $this->matchTokens($input);

        if (!empty($inputAlias)) {
            $inputTokens = array_values(array_unique(array_merge($inputTokens, $this->matchTokens($inputAlias))));
        }

        // Tentative 1 : Correspondance exacte après normalisation
        foreach ($collection as $name => $id) {
            if ($this->normalizeString($name) === $inputNormalized) {
                return $id;
            }
        }

        // Tentative 1b : correspondance via alias métier
        if (!empty($inputAlias)) {
            foreach ($collection as $name => $id) {
                $candidateNormalized = $this->normalizeString($name);
                if (str_contains($candidateNormalized, $this->normalizeString($inputAlias))) {
                    return $id;
                }
            }
        }

        // Tentative 2 : L'entrée est contenue dans le nom de la base (ex: "Sante" dans "Ministère de la Santé")
        foreach ($collection as $name => $id) {
            if (str_contains($this->normalizeString($name), $inputNormalized)) {
                return $id;
            }
        }

        // Tentative 3 : Le nom de la base est contenu dans l'entrée
        foreach ($collection as $name => $id) {
            if (str_contains($inputNormalized, $this->normalizeString($name))) {
                return $id;
            }
        }

        // Tentative 4 : comparaison souple par mots-clés communs
        $bestId = null;
        $bestScore = 0;

        foreach ($collection as $name => $id) {
            $candidateTokens = $this->matchTokens($name);
            $score = count(array_intersect($inputTokens, $candidateTokens));

            if ($score === 0) {
                $candidateNormalized = $this->normalizeString($name);
                foreach ($inputTokens as $token) {
                    if (strlen($token) >= 3 && str_contains($candidateNormalized, $token)) {
                        $score++;
                    }
                }
            }

            if ($score === 0) {
                foreach ($inputTokens as $inputToken) {
                    if (strlen($inputToken) < 5) {
                        continue;
                    }

                    foreach ($candidateTokens as $candidateToken) {
                        if (strlen($candidateToken) < 5) {
                            continue;
                        }

                        if (levenshtein($inputToken, $candidateToken) <= 2) {
                            $score = 1;
                            break 2;
                        }
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestId = $id;
            }
        }

        return $bestScore > 0 ? $bestId : null;

    }

    private function matchTokens(string $value): array
    {
        $normalized = $this->normalizeTokenText($value);
        $tokens = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $stopWords = [
            'ministere', 'ministerial', 'ministre', 'de', 'du', 'des', 'd', 'l', 'la', 'le', 'les',
            'en', 'et', 'a', 'au', 'aux', 'charge', 'chargee', 'chargees', 'public', 'publique',
            'general', 'generale', 'generaux', 'national', 'nationale', 'direction', 'directions'
        ];

        return array_values(array_filter($tokens, function ($token) use ($stopWords) {
            return $token !== '' && !in_array($token, $stopWords, true);
        }));
    }

    private function normalizeTokenText(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'å', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'è', 'é', 'ê', 'ë', 'ç', 'ì', 'í', 'î', 'ï', 'ù', 'ú', 'û', 'ü', 'ÿ', 'ñ', '’', "'"],
            ['a', 'a', 'a', 'a', 'a', 'a', 'o', 'o', 'o', 'o', 'o', 'o', 'e', 'e', 'e', 'e', 'c', 'i', 'i', 'i', 'i', 'u', 'u', 'u', 'u', 'y', 'n', ' ', ' '],
            $str
        );

        $str = preg_replace('/[^a-z0-9]+/u', ' ', $str) ?? $str;

        return trim(preg_replace('/\s+/', ' ', $str) ?? $str);
    }

    private function normalizeMinistereAlias(string $value): string
    {
        $normalized = $this->normalizeTokenText($value);

        foreach ($this->ministereAliases as $needle => $replacement) {
            if (str_contains($normalized, $needle)) {
                return $replacement;
            }
        }

        return '';
    }

    private function normalizeProfilAlias(string $value): string
    {
        $normalized = $this->normalizeTokenText($value);

        foreach ($this->profilAliases as $needle => $replacement) {
            if (str_contains($normalized, $this->normalizeTokenText($needle))) {
                return $replacement;
            }
        }

        return '';
    }

    private function normalizeString($str)
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'å', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'è', 'é', 'ê', 'ë', 'ç', 'ì', 'í', 'î', 'ï', 'ù', 'ú', 'û', 'ü', 'ÿ', 'ñ'],
            ['a', 'a', 'a', 'a', 'a', 'a', 'o', 'o', 'o', 'o', 'o', 'o', 'e', 'e', 'e', 'e', 'c', 'i', 'i', 'i', 'i', 'u', 'u', 'u', 'u', 'y', 'n'],
            $str
        );
        return preg_replace('/[^a-z0-9]/', '', $str); // On ne garde que les lettres et chiffres pour comparer
    }
}
