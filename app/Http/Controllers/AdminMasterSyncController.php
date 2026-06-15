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
     * Phase 1 : Analyse et Prévisualisation (Mapping aligné sur AgentsImport)
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

            // On utilise la classe d'importation pour garantir le WithHeadingRow
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

            foreach ($rows as $index => $row) {
                $parsed = $import->parseRow($row, $index + 2);
                if (!$parsed['can_sync']) continue;

                if ($defaultMinId) $parsed['ministere_id'] = $defaultMinId;

                // Recherche avec tolérance aux fautes
                $user = null;
                $confidence = 'none';

                if (!empty($parsed['matricule'])) {
                    $user = User::where('matricule', $parsed['matricule'])->first();
                    if ($user) $confidence = 'high';
                }

                if (!$user && !empty($parsed['telephone'])) {
                    $user = User::where('telephone', $parsed['telephone'])->first();
                    if ($user) $confidence = 'high';
                }

                if (!$user && !empty($parsed['prenom']) && !empty($parsed['nom'])) {
                    $p = strtolower(trim(preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $parsed['prenom'])));
                    $n = strtolower(trim(preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $parsed['nom'])));
                    $minId = $parsed['ministere_id'];

                    $queryBuilder = function($q) use ($p, $n) {
                        $q->where(function($sq) use ($p, $n) {
                            $sq->where(function($ssq) use ($n) {
                                $ssq->whereRaw('LOWER(nom) LIKE ?', ["%$n%"])->orWhereRaw('? LIKE CONCAT("%", LOWER(nom), "%")', [$n]);
                            })->where(function($ssq) use ($p) {
                                $ssq->whereRaw('LOWER(prenom) LIKE ?', ["%$p%"])->orWhereRaw('? LIKE CONCAT("%", LOWER(prenom), "%")', [$p]);
                            });
                        })->orWhere(function($sq) use ($p, $n) {
                            $sq->where(function($ssq) use ($p) {
                                $ssq->whereRaw('LOWER(nom) LIKE ?', ["%$p%"])->orWhereRaw('? LIKE CONCAT("%", LOWER(nom), "%")', [$p]);
                            })->where(function($ssq) use ($n) {
                                $ssq->whereRaw('LOWER(prenom) LIKE ?', ["%$n%"])->orWhereRaw('? LIKE CONCAT("%", LOWER(prenom), "%")', [$n]);
                            });
                        });
                    };

                    // 1. Recherche PRIORITAIRE au sein du MINISTÈRE (si défini)
                    if ($minId) {
                        $user = User::where('ministere_id', $minId)->where($queryBuilder)->first();
                        if ($user) $confidence = 'high';
                    }

                    // 2. Recherche ÉLARGIE à toute la base (si non trouvé dans le ministère)
                    if (!$user) {
                        $user = User::where($queryBuilder)->first();
                        if ($user) {
                            $confidence = ($minId && $user->ministere_id == $minId) ? 'high' : 'medium';
                        }
                    }
                }

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
                    'parsed_json' => base64_encode(json_encode($parsed)) // Sécurité HTML
                ];

                if (count($analysis['details']) < 1000) {
                    $analysis['details'][] = $rowDetail;
                }
            }

            if ($request->input('reset_mode') === 'reset') {
                $analysis['reserve_impact'] = User::count() - $analysis['confirmed'];
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
        $rows = $request->input('rows');
        $validated = $request->input('validated', []);
        $preview = session('master_sync_preview');

        if (!$preview) return back()->with('error', 'Session expirée.');

        try {
            DB::transaction(function () use ($rows, $validated, $preview) {
                // 1. Réinitialisation si mode Nettoyage
                if ($preview['reset_mode'] === 'reset') {
                    User::query()->update(['validation_status' => 'reserve', 'validation_source' => null]);
                }

                foreach ($rows as $idx => $row) {
                    $parsed = json_decode(base64_decode($row['parsed_b64']), true);
                    $isValidated = isset($validated[$idx]);
                    $userId = $row['user_id'];

                    // SI VALIDÉ ET USER TROUVÉ -> MISE À JOUR (FUSION)
                    if ($isValidated && $userId) {
                        $user = User::find($userId);
                        if ($user) {
                            $status = (!empty($user->experiences) || !empty($user->niveau_numerique)) ? 'officiel_inscrit' : 'officiel_attente';

                            $updateData = [
                                'ministere_id' => $parsed['ministere_id'],
                                'direction' => $parsed['direction'],
                                'metier' => $parsed['metier'],
                                'profil_id' => $parsed['profil_id'] ?: $user->profil_id,
                                'validation_status' => $status,
                                'validation_source' => $preview['source_name'],
                            ];

                            if ($parsed['profil_id'] && $user->profil_id != $parsed['profil_id']) {
                                if (!$user->profil_initial_id) $updateData['profil_initial_id'] = $user->profil_id;
                            }
                            $user->update($updateData);
                            continue;
                        }
                    }

                    // SI NON VALIDÉ (OU NON TROUVÉ) -> CRÉATION (Anti-doublon ultime)
                    // On vérifie une dernière fois en base avant de créer avec le queryBuilder robuste
                    $p = strtolower(trim(preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $parsed['prenom'])));
                    $n = strtolower(trim(preg_replace('/^(mme|m\.|m|monsieur|madame|mme\.)\s+/i', '', $parsed['nom'])));
                    
                    $exists = false;
                    if (!empty($parsed['matricule'])) {
                        $exists = User::where('matricule', $parsed['matricule'])->exists();
                    }
                    if (!$exists && !empty($p) && !empty($n)) {
                        $exists = User::where(function($q) use ($p, $n) {
                            $q->where(function($sq) use ($p, $n) {
                                $sq->whereRaw('LOWER(nom) LIKE ?', ["%$n%"])->orWhereRaw('? LIKE CONCAT("%", LOWER(nom), "%")', [$n]);
                            })->where(function($ssq) use ($p) {
                                $ssq->whereRaw('LOWER(prenom) LIKE ?', ["%$p%"])->orWhereRaw('? LIKE CONCAT("%", LOWER(prenom), "%")', [$p]);
                            });
                        })->exists();
                    }

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
                            'experiences' => [],
                            'competences_techniques' => [],
                        ]);
                    }
                }
            });

            if (isset($preview['path'])) Storage::delete($preview['path']);
            session()->forget('master_sync_preview');

            return redirect()->route('admin.master_sync.index')->with('success', 'Synchronisation finalisée selon vos choix.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erreur lors de la finalisation : ' . $e->getMessage());
        }
    }

    public function reset()
    {
        User::query()->update([
            'validation_status' => null,
            'validation_source' => null,
            'profil_initial_id' => null
        ]);

        return back()->with('success', 'Toutes les sources officielles ont été réinitialisées.');
    }

    public function cancel()
    {
        $preview = session('master_sync_preview');
        if ($preview && isset($preview['path'])) {
            Storage::delete($preview['path']);
        }
        session()->forget('master_sync_preview');

        return redirect()->route('admin.master_sync.index')->with('info', 'Synchronisation annulée.');
    }
}
