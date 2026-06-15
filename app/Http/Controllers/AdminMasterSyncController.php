<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profil;
use App\Models\Ministere;
use App\Imports\AgentsMasterSyncImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminMasterSyncController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => User::count(),
            'officiel_inscrit' => User::where('validation_status', 'officiel_inscrit')->count(),
            'officiel_attente' => User::where('validation_status', 'officiel_attente')->count(),
            'reserve' => User::where('validation_status', 'reserve')->count(),
            'non_defini' => User::whereNull('validation_status')->count(),
        ];

        $sources = User::whereNotNull('validation_source')
            ->distinct()
            ->pluck('validation_source');

        $preview = session('master_sync_preview');

        return view('admin.master-sync', compact('stats', 'sources', 'preview'));
    }

    /**
     * Phase 1 : Analyse et Prévisualisation
     */
    public function sync(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'source_name' => 'required|string|max:50',
            'reset_mode' => 'required|in:reset,additive',
            'default_ministere_id' => 'nullable|exists:ministeres,id',
        ]);

        try {
            $token = (string) Str::uuid();
            $file = $request->file('file');
            $storedPath = $file->storeAs('temp-sync', $token . '.' . $file->getClientOriginalExtension());
            $defaultMinId = $request->input('default_ministere_id');

            $import = new AgentsMasterSyncImport();
            $rows = Excel::toArray($import, Storage::path($storedPath))[0];

            $analysis = [
                'confirmed' => 0,
                'created' => 0,
                'conflicts' => 0,
                'reserve_impact' => 0,
                'total_rows' => count($rows),
                'details' => []
            ];

            // On récupère tous les candidats du ministère cible (pour le matching par jetons)
            $targetMinistereUsers = [];
            if ($defaultMinId) {
                $targetMinistereUsers = User::where('ministere_id', $defaultMinId)->get();
            } else {
                // Si pas de ministère par défaut, on prend tout pour le matching (plus lent mais plus sûr)
                $targetMinistereUsers = User::all();
            }

            foreach ($rows as $index => $row) {
                $parsed = $import->parseRow($row, $index + 2);
                if (!$parsed['can_sync']) continue;

                if ($defaultMinId) $parsed['ministere_id'] = $defaultMinId;

                $user = null;
                $confidence = 'none';

                // 1. Match Exact (Matricule/Tel)
                if (!empty($parsed['matricule'])) {
                    $user = User::where('matricule', $parsed['matricule'])->first();
                    if ($user) $confidence = 'high';
                }
                
                if (!$user && !empty($parsed['telephone'])) {
                    $user = User::where('telephone', $parsed['telephone'])->first();
                    if ($user) $confidence = 'high';
                }

                // 2. Match Intelligent (Tokens + Levenshtein)
                if (!$user && !empty($parsed['prenom']) && !empty($parsed['nom'])) {
                    $p = strtolower(trim(preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $parsed['prenom'])));
                    $n = strtolower(trim(preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $parsed['nom'])));
                    $tokens = array_filter(explode(' ', $p . ' ' . $n));

                    foreach ($targetMinistereUsers as $candidate) {
                        $candP = strtolower($candidate->prenom);
                        $candN = strtolower($candidate->nom);
                        
                        // Check exact (avec inversion possible)
                        if (($p == $candP && $n == $candN) || ($p == $candN && $n == $candP)) {
                            $user = $candidate;
                            $confidence = 'high';
                            break;
                        }

                        // Check Tokens
                        $candTokens = array_filter(explode(' ', $candP . ' ' . $candN));
                        $intersection = array_intersect($tokens, $candTokens);
                        
                        if (count($intersection) >= 2 || (count($tokens) == 1 && count($intersection) == 1)) {
                            $user = $candidate;
                            $confidence = 'medium';
                            break;
                        }

                        // Check Typos (Levenshtein)
                        $matchCount = 0;
                        foreach ($tokens as $t) {
                            foreach ($candTokens as $ct) {
                                if (levenshtein($t, $ct) <= 1) {
                                    $matchCount++;
                                    break;
                                }
                            }
                        }
                        if ($matchCount >= count($tokens) && count($tokens) > 0) {
                            $user = $candidate;
                            $confidence = 'medium';
                            break;
                        }
                    }
                }

                if ($user) $analysis['confirmed']++; else $analysis['created']++;

                $rowDetail = [
                    'id' => $index,
                    'name' => trim($parsed['prenom'] . ' ' . $parsed['nom']) ?: '—',
                    'matricule' => $parsed['matricule'] ?: '—',
                    'ministere' => $parsed['ministere_id'] ? optional(Ministere::find($parsed['ministere_id']))->nom : 'Non reconnu',
                    'profil_excel' => $parsed['profil_id'] ? optional(Profil::find($parsed['profil_id']))->libelle : 'Non reconnu',
                    'profil_actuel' => $user && $user->profil ? $user->profil->libelle : '—',
                    'existing_user_id' => $user ? $user->id : null,
                    'confidence' => $confidence,
                    'action' => $user ? 'Confirmation' : 'Création',
                    'parsed_json' => base64_encode(json_encode($parsed))
                ];

                if (count($analysis['details']) < 1000) {
                    $analysis['details'][] = $rowDetail;
                }
            }

            session([
                'master_sync_preview' => [
                    'token' => $token,
                    'path' => $storedPath,
                    'source_name' => $request->input('source_name'),
                    'reset_mode' => $request->input('reset_mode'),
                    'default_ministere_id' => $defaultMinId,
                    'analysis' => $analysis,
                ]
            ]);

            return back()->with('info', 'Analyse terminée. Veuillez vérifier le rapport avant de confirmer.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erreur lors de l\'analyse : ' . $e->getMessage());
        }
    }

    public function confirm(Request $request)
    {
        $rows = $request->input('rows', []);
        $validated = $request->input('validated', []);
        $preview = session('master_sync_preview');

        if (!$preview) return back()->with('error', 'Session expirée.');

        try {
            DB::transaction(function () use ($rows, $validated, $preview) {
                if ($preview['reset_mode'] === 'reset') {
                    User::query()->update(['validation_status' => 'reserve', 'validation_source' => null]);
                }

                $import = new AgentsMasterSyncImport();

                foreach ($rows as $idx => $row) {
                    $parsed = json_decode(base64_decode($row['parsed_b64']), true);
                    $isValidated = isset($validated[$idx]);
                    $userId = $row['user_id'];

                    if ($isValidated && $userId) {
                        $user = User::find($userId);
                        if ($user) {
                            $status = (!empty($user->experiences) || !empty($user->niveau_numerique)) ? 'officiel_inscrit' : 'officiel_attente';
                            $user->update([
                                'ministere_id' => $parsed['ministere_id'],
                                'direction' => $parsed['direction'],
                                'metier' => $parsed['metier'],
                                'profil_id' => $parsed['profil_id'] ?: $user->profil_id,
                                'validation_status' => $status,
                                'validation_source' => $preview['source_name'],
                            ]);
                            continue;
                        }
                    }

                    // Sécurité anti-doublon finale avec la logique intelligente
                    $exists = $import->findExistingUser($parsed);

                    if (!$exists) {
                        User::create([
                            'prenom' => $parsed['prenom'],
                            'nom' => $parsed['nom'],
                            'matricule' => $parsed['matricule'] ?: null,
                            'telephone' => $parsed['telephone'] ?: null,
                            'email' => $parsed['email'] ?: null,
                            'ministere_id' => $parsed['ministere_id'],
                            'direction' => $parsed['direction'],
                            'metier' => $parsed['metier'],
                            'profil_id' => $parsed['profil_id'],
                            'profil_initial_id' => $parsed['profil_id'],
                            'validation_status' => 'officiel_attente',
                            'validation_source' => $preview['source_name'],
                            'source_type' => 'master_sync',
                            'ready_to_deploy_all_regions' => true,
                            'disponibilite' => 'immediate',
                        ]);
                    }
                }
            });

            if (isset($preview['path'])) Storage::delete($preview['path']);
            session()->forget('master_sync_preview');
            return redirect()->route('admin.master_sync.index')->with('success', 'Synchronisation terminée.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function reset()
    {
        User::query()->update(['validation_status' => null, 'validation_source' => null]);
        return back()->with('success', 'Réinitialisé.');
    }

    public function cancel()
    {
        $preview = session('master_sync_preview');
        if ($preview && isset($preview['path'])) Storage::delete($preview['path']);
        session()->forget('master_sync_preview');
        return redirect()->route('admin.master_sync.index');
    }
}
