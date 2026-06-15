<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Profil;
use App\Models\Ministere;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class AgentsMasterSyncImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $profils;
    private $ministeres;
    private $sourceName;
    private $resetOthers;
    private $defaultMinistereId = null;
    private array $ministereAliases = [];
    private array $profilAliases = [];
    
    public $stats = [
        'confirmed' => 0,
        'created' => 0,
        'reserve' => 0,
    ];

    public function __construct(string $sourceName = 'Officiel', bool $resetOthers = true)
    {
        $this->sourceName = $sourceName;
        $this->resetOthers = $resetOthers;

        $this->profils = Profil::all()->pluck('id', 'libelle')->mapWithKeys(function ($id, $libelle) {
            return [strtolower(trim($libelle)) => $id];
        });
        
        $this->ministeres = Ministere::all()->pluck('id', 'nom')->mapWithKeys(function ($id, $nom) {
            return [strtolower(trim($nom)) => $id];
        });

        // Reprise EXACTE des alias de AgentsImport
        $this->ministereAliases = [
            'ministere en charge de la sante' => 'sante',
            'ministere en charge de la famille' => 'famille',
            'ministere en charge de la jeunesse' => 'jeunesse',
            'ministere en charge des affaires etrangeres' => 'affaires etrangeres',
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

        // Règles de mapping spécifiques demandées par l'utilisateur
        $this->profilAliases = [
            "chef d'equipe" => "chef d'équipe",
            'chefs d equipes' => "chef d'équipe",
            'chefs d equipe' => "chef d'équipe",
            'cheffe d equipe' => "chef d'équipe",
            'cheffe d\'equipe' => "chef d'équipe",
            'chef d\'équipe' => "chef d'équipe",
            'agent auditeur' => 'auditeur it',
            'agent auditeurs' => 'auditeur it',
            'auditeur' => 'auditeur it',
            'auditeurs' => 'auditeur it',
            'agent de support technique' => 'auditeur administratif',
            'agents de support technique' => 'auditeur administratif',
            'agent de support' => 'auditeur administratif',
            'agents de support' => 'auditeur administratif',
            'support technique' => 'auditeur administratif',
            'agent support technique' => 'auditeur administratif',
            'agent administratif' => 'auditeur administratif',
            'agent d appui' => 'auditeur administratif',
            'agent d\'appui' => 'auditeur administratif',
            'superviseurs' => 'superviseur',
            'superviseur' => 'superviseur',
            'chauffeurs' => 'chauffeur',
        ];
    }

    public function setDefaultMinistere($id)
    {
        $this->defaultMinistereId = $id;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            if ($this->resetOthers) {
                User::query()->update(['validation_status' => 'reserve', 'validation_source' => null]);
            }

            foreach ($rows as $index => $rowCollection) {
                $row = $rowCollection->toArray();
                $parsed = $this->parseRow($row, $index + 2);

                if (!$parsed['can_sync']) continue;

                // Application du ministère par défaut
                if ($this->defaultMinistereId) {
                    $parsed['ministere_id'] = $this->defaultMinistereId;
                }

                $user = $this->findExistingUser($parsed);
                $status = ($user && (!empty($user->experiences) || !empty($user->niveau_numerique))) 
                    ? 'officiel_inscrit' 
                    : 'officiel_attente';

                $data = [
                    'prenom' => $parsed['prenom'],
                    'nom' => $parsed['nom'],
                    'matricule' => $parsed['matricule'] ?: ($user ? $user->matricule : null),
                    'telephone' => $parsed['telephone'] ?: ($user ? $user->telephone : null),
                    'email' => $parsed['email'] ?: ($user ? $user->email : null),
                    'ministere_id' => $parsed['ministere_id'],
                    'direction' => $parsed['direction'],
                    'metier' => $parsed['metier'],
                    'profil_id' => $parsed['profil_id'] ?: ($user ? $user->profil_id : null),
                    'validation_status' => $status,
                    'validation_source' => $this->sourceName,
                ];

                if ($user) {
                    if ($parsed['profil_id'] && $user->profil_id != $parsed['profil_id']) {
                        if (!$user->profil_initial_id) $data['profil_initial_id'] = $user->profil_id;
                    }
                    $user->update($data);
                    $this->stats['confirmed']++;
                } else {
                    User::create(array_merge($data, [
                        'profil_initial_id' => $parsed['profil_id'],
                        'source_type' => 'master_sync',
                        'ready_to_deploy_all_regions' => true,
                        'disponibilite' => 'immediate',
                        'experiences' => [],
                        'competences_techniques' => [],
                    ]));
                    $this->stats['created']++;
                }
            }
            $this->stats['reserve'] = User::where('validation_status', 'reserve')->count();
        });
    }

    public function parseRow(array $row, ?int $line = null): array
    {
        $normalize = function ($v) {
            $s = is_string($v) ? trim($v) : trim((string)$v);
            $low = strtolower($s);
            $emptyTokens = ['null', 'n/a', 'na', '-', '—', '–', 'aucun', 'sans', 'none', ''];
            if (in_array($low, $emptyTokens, true)) return '';
            return $s;
        };

        $membres = $normalize($row['membres'] ?? $row['nom_complet'] ?? $row['membre'] ?? '');
        $prenom = $normalize($row['prenom'] ?? '');
        $nom = $normalize($row['nom'] ?? '');

        if ($prenom === '' && $nom === '' && $membres !== '') {
            [$prenom, $nom] = $this->splitFullName($membres);
        }

        $matricule = $this->normalizeMatricule($row['matricule'] ?? $row['cin'] ?? '');
        $telephone = $normalize($row['telephone'] ?? '');
        
        $ministereInput = strtolower($normalize($row['structure'] ?? $row['ministere'] ?? ''));
        $direction = $normalize($row['drection'] ?? $row['direction'] ?? '');
        $metier = $normalize($row['metier'] ?? $row['fonction'] ?? $row['profession'] ?? '');
        
        $profilInput = strtolower($normalize($row['profil'] ?? ''));
        $profilAlias = $this->normalizeProfilAlias($profilInput);
        if (!empty($profilAlias)) {
            $profilInput = $profilAlias;
        }

        return [
            'prenom' => $prenom,
            'nom' => $nom,
            'matricule' => $matricule,
            'telephone' => $telephone,
            'email' => $normalize($row['email'] ?? ''),
            'direction' => $direction,
            'metier' => $metier,
            'profil_id' => $this->findBestMatch($this->profils, $profilInput),
            'ministere_id' => $this->findBestMatch($this->ministeres, $ministereInput),
            'can_sync' => (!empty($matricule) || !empty($telephone) || (!empty($prenom) && !empty($nom))),
        ];
    }

    private function findExistingUser(array $parsed)
    {
        if (!empty($parsed['matricule'])) return User::where('matricule', $parsed['matricule'])->first();
        if (!empty($parsed['telephone'])) return User::where('telephone', $parsed['telephone'])->first();
        
        // Recherche contextuelle par Nom+Prénom
        if (!empty($parsed['prenom']) && !empty($parsed['nom'])) {
            $p = $parsed['prenom'];
            $n = $parsed['nom'];
            $minId = $parsed['ministere_id'];

            // Nettoyage des titres pour le matching
            $cleanP = preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $p);
            $cleanN = preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $n);

            // 1. Priorité au ministère
            if ($minId) {
                $u = User::where('ministere_id', $minId)
                    ->where(function($q) use ($cleanP, $cleanN) {
                        $q->where(function($sq) use ($cleanP, $cleanN) {
                            $sq->where(function($ssq) use ($cleanN) {
                                $ssq->where('nom', 'like', "%$cleanN%")->orWhereRaw('? LIKE CONCAT("%", nom, "%")', [$cleanN]);
                            })->where(function($ssq) use ($cleanP) {
                                $ssq->where('prenom', 'like', "%$cleanP%")->orWhereRaw('? LIKE CONCAT("%", prenom, "%")', [$cleanP]);
                            });
                        })->orWhere(function($sq) use ($cleanP, $cleanN) {
                            $sq->where(function($ssq) use ($cleanP) {
                                $ssq->where('nom', 'like', "%$cleanP%")->orWhereRaw('? LIKE CONCAT("%", nom, "%")', [$cleanP]);
                            })->where(function($ssq) use ($cleanN) {
                                $ssq->where('prenom', 'like', "%$cleanN%")->orWhereRaw('? LIKE CONCAT("%", prenom, "%")', [$cleanN]);
                            });
                        });
                    })->first();
                if ($u) return $u;
            }

            // 2. Recherche globale
            return User::where(function($q) use ($cleanP, $cleanN) {
                $q->where(function($sq) use ($cleanP, $cleanN) {
                    $sq->where(function($ssq) use ($cleanN) {
                        $ssq->where('nom', 'like', "%$cleanN%")->orWhereRaw('? LIKE CONCAT("%", nom, "%")', [$cleanN]);
                    })->where(function($ssq) use ($cleanP) {
                        $ssq->where('prenom', 'like', "%$cleanP%")->orWhereRaw('? LIKE CONCAT("%", prenom, "%")', [$cleanP]);
                    });
                })->orWhere(function($sq) use ($cleanP, $cleanN) {
                    $sq->where(function($ssq) use ($cleanP) {
                        $ssq->where('nom', 'like', "%$cleanP%")->orWhereRaw('? LIKE CONCAT("%", nom, "%")', [$cleanP]);
                    })->where(function($ssq) use ($cleanN) {
                        $ssq->where('prenom', 'like', "%$cleanN%")->orWhereRaw('? LIKE CONCAT("%", prenom, "%")', [$cleanN]);
                    });
                });
            })->first();
        }
        return null;
    }

    private function splitFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        if (count($parts) <= 1) return [trim($fullName), ''];
        $nom = array_pop($parts);
        $prenom = implode(' ', $parts);
        return [trim($prenom), trim($nom)];
    }

    private function normalizeMatricule($value): string
    {
        $matricule = strtoupper(trim((string) $value));
        $matricule = preg_replace('/[\s\/]+/', '', $matricule) ?? $matricule;
        if ($matricule === '' || !preg_match('/^[0-9]{6}[A-Z]$/', $matricule)) return '';
        return $matricule;
    }

    private function findBestMatch($collection, $input)
    {
        if (empty($input)) return null;
        $inputNormalized = $this->normalizeString($input);
        $inputAlias = $this->normalizeMinistereAlias($input);
        $inputTokens = $this->matchTokens($input);
        if (!empty($inputAlias)) $inputTokens = array_values(array_unique(array_merge($inputTokens, $this->matchTokens($inputAlias))));

        foreach ($collection as $name => $id) { if ($this->normalizeString($name) === $inputNormalized) return $id; }
        if (!empty($inputAlias)) { foreach ($collection as $name => $id) { if (str_contains($this->normalizeString($name), $this->normalizeString($inputAlias))) return $id; } }
        foreach ($collection as $name => $id) { if (str_contains($this->normalizeString($name), $inputNormalized)) return $id; }

        $bestId = null; $bestScore = 0;
        foreach ($collection as $name => $id) {
            $candidateTokens = $this->matchTokens($name);
            $score = count(array_intersect($inputTokens, $candidateTokens));
            if ($score > $bestScore) { $bestScore = $score; $bestId = $id; }
        }
        return $bestScore > 0 ? $bestId : null;
    }

    private function matchTokens(string $value): array
    {
        $normalized = $this->normalizeTokenText($value);
        $tokens = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $stopWords = ['ministere', 'de', 'du', 'des', 'd', 'l', 'la', 'le', 'les', 'en', 'et', 'a', 'au', 'aux', 'charge'];
        return array_values(array_filter($tokens, function ($token) use ($stopWords) { return $token !== '' && !in_array($token, $stopWords, true); }));
    }

    private function normalizeTokenText(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = str_replace(['’', "'"], [' ', ' '], $str);
        return trim($this->normalizeString($str));
    }

    private function normalizeProfilAlias(string $value): string
    {
        $normalized = $this->normalizeString($value);
        foreach ($this->profilAliases as $needle => $replacement) { if (str_contains($normalized, $this->normalizeString($needle))) return $replacement; }
        return '';
    }

    private function normalizeMinistereAlias(string $value): string
    {
        $normalized = $this->normalizeString($value);
        foreach ($this->ministereAliases as $needle => $replacement) { if (str_contains($normalized, $this->normalizeString($needle))) return $replacement; }
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
        return preg_replace('/[^a-z0-9]/', ' ', $str);
    }
}
