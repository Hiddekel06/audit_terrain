<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profil;
use App\Models\Ministere;
use App\Models\Region;
use App\Models\UserRegionChoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Imports\AgentsImport;
use App\Imports\AgentsPhoneUpdateImport;
use App\Imports\AgentsPreviewImport;
use App\Exports\CandidatesExport;
use App\Exports\ImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminCandidateController extends Controller
{
    /**
     * Retourne les ministères affichables dans les listes de sélection.
     */
    private function selectableMinisteres()
    {
        return Ministere::query()
            ->whereRaw("NOT (LOWER(nom) LIKE '%education%' AND LOWER(nom) NOT LIKE '%nationale%')")
            ->orderBy('nom')
            ->get();
    }

    /**
     * Construit la requête candidats avec les filtres actifs.
     */
    private function buildCandidatesQuery(Request $request)
    {
        $query = User::with([
            'profil',
            'ministere',
            'regionChoices' => function ($regionQuery) {
                $regionQuery->orderBy('ordre');
            },
            'regionChoices.region',
        ]);

        if ($request->filled('profil_id')) {
            if ($request->profil_id === '_none_') {
                $query->whereNull('profil_id');
            } else {
                $query->where('profil_id', $request->profil_id);
            }
        }

        if ($request->filled('profile_completion')) {
            if ($request->profile_completion === 'incomplete') {
                $query->where(function($q) {
                    $q->whereNull('niveau_numerique')
                      ->where(function($sq) {
                          $sq->whereNull('experiences')->orWhere('experiences', '[]')->orWhere('experiences', '');
                      });
                });
            } elseif ($request->profile_completion === 'completed') {
                $query->where(function($q) {
                    $q->whereNotNull('niveau_numerique')
                      ->orWhere(function($sq) {
                          $sq->whereNotNull('experiences')->where('experiences', '!=', '[]')->where('experiences', '!=', '');
                      });
                });
            }
        }

        if ($request->filled('niveau_numerique')) {
            $query->where('niveau_numerique', $request->niveau_numerique);
        }

        if ($request->filled('ministere_id')) {
            $query->where('ministere_id', $request->ministere_id);
        }

        if ($request->filled('direction')) {
            if ($request->direction === '_none_') {
                $query->where(function ($q) {
                    $q->whereNull('direction')
                        ->orWhere('direction', '');
                });
            } else {
                $query->where('direction', $request->direction);
            }
        }

        if ($request->filled('telephone_status')) {
            if ($request->telephone_status === 'missing') {
                $query->where(function ($q) {
                    $q->whereNull('telephone')
                        ->orWhere('telephone', '');
                });
            } elseif ($request->telephone_status === 'present') {
                $query->where(function ($q) {
                    $q->whereNotNull('telephone')
                        ->where('telephone', '!=', '');
                });
            }
        }

        if ($request->filled('experience')) {
            $query->whereJsonContains('experiences', $request->experience);
        }

        if ($request->filled('region_id')) {
            $regionId = $request->region_id;
            $query->whereHas('regionChoices', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        if ($request->filled('ready_to_deploy')) {
            $readyToDeploy = $request->ready_to_deploy === 'yes';
            $query->where('ready_to_deploy_all_regions', $readyToDeploy);
        }

        if ($request->filled('validation_status')) {
            if ($request->validation_status === '_none_') {
                $query->whereNull('validation_status');
            } else {
                $query->where('validation_status', $request->validation_status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                    ->orWhere('prenom', 'like', "%$search%")
                    ->orWhere('matricule', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        return $query;
    }

    /**
     * Affiche la liste de tous les candidats avec filtres.
     */
    public function index(Request $request)
    {
        $query = $this->buildCandidatesQuery($request);
        $candidates = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Données pour les filtres
        $profils = Profil::where('is_active', true)->get();
        $ministeres = $this->selectableMinisteres();
        $directions = User::whereNotNull('direction')
            ->where('direction', '!=', '')
            ->distinct()
            ->orderBy('direction')
            ->pluck('direction');
        $regions = Region::orderBy('nom')->get();
        $niveauxNumeriques = ['debutant', 'intermediaire', 'avance', 'expert'];
        $experiences = [
            'audit_recensement' => 'Audit / recensement',
            'biometrie' => 'Biométrie',
            'projets_it' => 'Projets IT',
            'aucune' => 'Aucune'
        ];
        $deploymentOptions = [
            'yes' => 'Oui, toutes les régions',
            'no' => 'Non, une seule région',
        ];

        return view('admin.candidates.index', compact(
            'candidates',
            'profils',
            'ministeres',
            'directions',
            'regions',
            'niveauxNumeriques',
            'experiences',
            'deploymentOptions'
        ));
    }

    /**
     * Exporte les candidats filtrés au format Excel.
     */
    public function export(Request $request)
    {
        $candidates = $this->buildCandidatesQuery($request)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'candidats_filtres_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new CandidatesExport($candidates), $filename);
    }

    /**
     * Affiche le formulaire de création manuelle d'un agent.
     */
    public function create()
    {
        $profils = Profil::where('is_active', true)->orderBy('ordre')->get();
        $ministeres = $this->selectableMinisteres();
        
        return view('admin.candidates.create', compact('profils', 'ministeres'));
    }

    /**
     * Enregistre un nouvel agent ajouté manuellement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'telephone' => ['nullable', 'required_without:matricule', 'digits:9', 'unique:users,telephone'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'ministere_id' => ['required', 'exists:ministeres,id'],
            'direction' => ['nullable', 'string', 'max:255'],
            'metier' => ['nullable', 'string', 'max:255'],
            // Matricule: 6 chiffres + 1 lettre majuscule
            'matricule' => ['nullable', 'required_without:telephone', 'unique:users,matricule', 'regex:/^[0-9]{6}[A-Z]$/'],
            'profil_id' => ['required', 'exists:profil,id'],
            
            // Facultatifs
            'niveau_numerique' => ['nullable', 'in:debutant,intermediaire,avance,expert'],
            'experiences' => ['nullable', 'array'],
            'competences_techniques' => ['nullable', 'array'],
        ], [
            'telephone.digits' => 'Le numéro de téléphone doit contenir exactement 9 chiffres.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé par un autre agent.',
            'telephone.required_without' => 'Renseignez un numéro de téléphone ou un matricule.',
            'matricule.unique' => 'Ce matricule est déjà utilisé par un autre agent.',
            'matricule.regex' => 'Le matricule doit être au format 6 chiffres suivis d\'une lettre majuscule, par ex. 123456A.',
            'matricule.required_without' => 'Renseignez un matricule ou un numéro de téléphone.',
        ]);

        $user = User::create([
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'matricule' => strtoupper(trim($validated['matricule'])),
            'telephone' => $validated['telephone'],
            'email' => $validated['email'],
            'ministere_id' => $validated['ministere_id'],
            'direction' => trim($validated['direction']),
            'metier' => trim($validated['metier'] ?? ''),
            'profil_id' => $validated['profil_id'],
            'profil_initial_id' => $validated['profil_id'],

            'niveau_numerique' => $validated['niveau_numerique'] ?? null,
            'source_type' => 'manual',
            'experiences' => $validated['experiences'] ?? [],
            'competences_techniques' => $validated['competences_techniques'] ?? [],
            
            'ready_to_deploy_all_regions' => true,
            'disponibilite' => 'immediate',
        ]);

        return redirect()->route('admin.candidates.index')
            ->with('success', "L'agent {$user->prenom} {$user->nom} a été ajouté avec succès.")
            ->with('created_user_id', $user->id);
    }

    /**
     * Vérifie l'existence d'un matricule (utilisé en AJAX côté client pour éviter doublons).
     */
    public function checkMatricule(Request $request)
    {
        $matricule = strtoupper(trim($request->query('matricule', '')));
        if ($matricule === '') {
            return response()->json(['exists' => false]);
        }

        $exists = \App\Models\User::where('matricule', $matricule)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Gère l'importation massive d'agents via un fichier Excel.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:5120',
            'import_mode' => 'nullable|in:classic,phone_update',
        ]);

        try {
            $mode = $request->input('import_mode', 'classic');
            $token = (string) Str::uuid();
            $file = $request->file('excel_file');
            $storedPath = $file->storeAs('import-previews', $token . '.' . $file->getClientOriginalExtension());

            $previewImport = new AgentsPreviewImport();
            Excel::import($previewImport, Storage::path($storedPath));

            $previewRows = [];

            if ($mode === 'phone_update') {
                foreach ($previewImport->rows as $index => $row) {
                    $line = is_numeric($index) ? $index + 2 : null;
                    $matricule = strtoupper(trim((string) ($row['matricule'] ?? $row['cin'] ?? '')));
                    $matricule = preg_replace('/[\s\/]+/', '', $matricule) ?? $matricule;
                    $telephone = preg_replace('/\D+/', '', (string) ($row['telephone'] ?? $row['tel'] ?? $row['numero_telephone'] ?? '')) ?? '';
                    if (strlen($telephone) > 9) {
                        $telephone = substr($telephone, -9);
                    }

                    $matchedUser = $matricule !== '' ? User::where('matricule', $matricule)->first() : null;
                    $currentTelephone = $matchedUser?->telephone;

                    $issues = [];
                    if ($matricule === '') {
                        $issues[] = 'Matricule manquant';
                    }
                    if ($telephone === '') {
                        $issues[] = 'Téléphone manquant';
                    }
                    if ($matricule !== '' && !$matchedUser) {
                        $issues[] = 'Matricule introuvable';
                    }

                    $status = empty($issues) ? 'ok' : 'error';

                    $previewRows[] = [
                        'line' => $line,
                        'matricule' => $matricule,
                        'telephone' => $telephone,
                        'current_telephone' => $currentTelephone,
                        'issues' => $issues,
                        'warnings' => [],
                        'status' => $status,
                        'can_import' => $status === 'ok',
                    ];
                }
            } else {
                $parser = new AgentsImport();

                foreach ($previewImport->rows as $index => $row) {
                    $parsed = $parser->parseRow($row, is_numeric($index) ? $index + 2 : null);
                    $parsed['ministere_reconnu'] = $parsed['ministere_id']
                        ? optional(Ministere::find($parsed['ministere_id']))->nom
                        : null;
                    $parsed['profil_reconnu'] = $parsed['profil_id']
                        ? optional(Profil::find($parsed['profil_id']))->libelle
                        : null;
                    $parsed['profil_secondaires'] = array_values(array_filter(array_map(function ($pId) {
                        $p = Profil::find($pId);
                        return $p ? $p->libelle : null;
                    }, $parsed['profil_secondaires'] ?? [])));
                    $previewRows[] = $parsed;
                }
            }

            session([
                'import_preview' => [
                    'token' => $token,
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mode' => $mode,
                    'rows' => $previewRows,
                    'total' => count($previewRows),
                    'valid' => collect($previewRows)->where('can_import', true)->count(),
                    'invalid' => collect($previewRows)->where('can_import', false)->count(),
                    'warnings' => $mode === 'phone_update'
                        ? 0
                        : collect($previewRows)->filter(fn ($row) => ($row['status'] ?? '') === 'warning')->count(),
                ],
            ]);

            return back()->with('info', $mode === 'phone_update'
                ? 'Prévisualisation prête. Vérifie les matricules puis confirme la mise à jour.'
                : 'Prévisualisation prête. Vérifie le rendu puis confirme l’import.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'importation : ' . $e->getMessage());
        }
    }

    /**
     * Lance l'import réel après validation de l'aperçu.
     */
    public function confirmImport(Request $request)
    {
        $request->validate([
            'preview_token' => 'required|string',
        ]);

        $preview = session('import_preview');

        if (!$preview) {
            return redirect()->route('admin.candidates.create')->with('error', 'Aucun aperçu d\'import trouvé en session. Recharge le fichier Excel.');
        }

        $provided = $request->input('preview_token');
        $expected = $preview['token'] ?? null;
        if (!hash_equals((string)$expected, (string)$provided)) {
            \Log::warning('Import preview token mismatch', ['expected' => $expected, 'provided' => $provided]);
            return redirect()->route('admin.candidates.create')->with('error', 'Jeton d\'aperçu invalide. Recharge l\'aperçu puis réessaie.');
        }

        // Ensure stored file still exists
        $storedPath = $preview['path'] ?? null;
        if (!$storedPath || !\Storage::exists($storedPath)) {
            \Log::warning('Import preview file missing', ['path' => $storedPath]);
            return redirect()->route('admin.candidates.create')->with('error', 'Fichier d\'aperçu introuvable sur le serveur. Recharge le fichier Excel.');
        }

        try {
            $import = ($preview['mode'] ?? 'classic') === 'phone_update'
                ? new AgentsPhoneUpdateImport()
                : new AgentsImport();

            Excel::import($import, Storage::path($preview['path']));

            Storage::delete($preview['path']);
            session()->forget('import_preview');

            // Save full skipped list in session for review
            session(['import_skipped' => $import->skippedAgents]);

            if (($preview['mode'] ?? 'classic') === 'phone_update') {
                $msg = $import->updatedCount . ' numéros de téléphone ont été mis à jour avec succès.';
            } else {
                $msg = $import->importedCount . ' agents ont été importés avec succès.';
            }

            // no temporary matricules generated

            $skippedCount = count($import->skippedAgents);
            if ($skippedCount > 0) {
                $displayCount = min(20, $skippedCount);
                $displayList = array_slice($import->skippedAgents, 0, $displayCount);
                $msg .= ' Attention : ' . $skippedCount . ' lignes ont été ignorées (' . implode('; ', $displayList) . ( $skippedCount > $displayCount ? ' ...' : '' ) . ').';
            }

            return redirect()->route('admin.candidates.index')->with('success', $msg)->with('import_skipped_count', $skippedCount);
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'importation : ' . $e->getMessage());
        }
    }

    /**
     * Annule l'aperçu d'import courant.
     */
    public function cancelImport()
    {
        $preview = session('import_preview');

        if ($preview && !empty($preview['path'])) {
            Storage::delete($preview['path']);
        }

        session()->forget('import_preview');

        return redirect()->route('admin.candidates.create')->with('info', 'Aperçu d’import annulé.');
    }

    /**
     * Génère et télécharge un modèle Excel vide pour l'importation.
     */
    public function downloadTemplate(Request $request)
    {
        $mode = $request->query('mode', 'classic');

        if ($mode === 'phone_update') {
            $headers = ['Matricule', 'Telephone'];
            $filename = 'modele_maj_telephone.xlsx';
        } else {
            $headers = [
                'Membres', 'Matricule', 'Email', 'Telephone', 'Structure', 'Direction', 'Metier', 'Profil'
            ];
            $filename = 'modele_import_agents.xlsx';
        }

        return Excel::download(new ImportTemplateExport($headers), $filename);
    }

    /**
     * Affiche le détail d'un candidat.
     */
    public function show(Request $request, User $user)
    {
        $user->load(['profil', 'ministere', 'regionChoices.region', 'dynamicAnswers']);

        $themeClass = 'theme-default';
        $profilLabel = strtolower($user->profil->libelle ?? '');

        if (str_contains($profilLabel, 'chef')) {
            $themeClass = 'theme-chef';
        } elseif (str_contains($profilLabel, 'auditeur')) {
            $themeClass = 'theme-auditeur';
        } elseif (str_contains($profilLabel, 'support')) {
            $themeClass = 'theme-support';
        }

        // Formater les données
        $experiences = $user->experiences ?? [];
        $competencesTechniques = $user->competences_techniques ?? [];

        // Choix régionaux avec motivations
        $regionalChoices = $user->regionChoices()
            ->with('region', 'motivations')
            ->orderBy('ordre')
            ->get();

        // Construire la même base de filtres que dans index pour respecter le contexte
        $baseQuery = User::query();
        if ($request->filled('profil_id')) {
            $baseQuery->where('profil_id', $request->profil_id);
        }
        if ($request->filled('niveau_numerique')) {
            $baseQuery->where('niveau_numerique', $request->niveau_numerique);
        }
        if ($request->filled('ministere_id')) {
            $baseQuery->where('ministere_id', $request->ministere_id);
        }
        if ($request->filled('direction')) {
            if ($request->direction === '_none_') {
                $baseQuery->where(function ($q) {
                    $q->whereNull('direction')->orWhere('direction', '');
                });
            } else {
                $baseQuery->where('direction', $request->direction);
            }
        }
        if ($request->filled('telephone_status')) {
            if ($request->telephone_status === 'missing') {
                $baseQuery->where(function ($q) {
                    $q->whereNull('telephone')->orWhere('telephone', '');
                });
            } elseif ($request->telephone_status === 'present') {
                $baseQuery->where(function ($q) {
                    $q->whereNotNull('telephone')->where('telephone', '!=', '');
                });
            }
        }
        if ($request->filled('experience')) {
            $baseQuery->whereJsonContains('experiences', $request->experience);
        }
        if ($request->filled('region_id')) {
            $regionId = $request->region_id;
            $baseQuery->whereHas('regionChoices', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }
        if ($request->filled('ready_to_deploy')) {
            $readyToDeploy = $request->ready_to_deploy === 'yes';
            $baseQuery->where('ready_to_deploy_all_regions', $readyToDeploy);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                    ->orWhere('prenom', 'like', "%$search%")
                    ->orWhere('matricule', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Assurer le même ordre que la liste (created_at desc)
        // Calculer précédent (plus récent) et suivant (plus ancien)
        $prev = (clone $baseQuery)
            ->where(function ($q) use ($user) {
                $q->where('created_at', '>', $user->created_at)
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('created_at', $user->created_at)->where('id', '>', $user->id);
                  });
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first(['id']);

        $next = (clone $baseQuery)
            ->where(function ($q) use ($user) {
                $q->where('created_at', '<', $user->created_at)
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('created_at', $user->created_at)->where('id', '<', $user->id);
                  });
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first(['id']);

        $prevId = $prev->id ?? null;
        $nextId = $next->id ?? null;

        return view('admin.candidates.show', compact(
            'user',
            'experiences',
            'competencesTechniques',
            'regionalChoices',
            'prevId',
            'nextId',
            'themeClass'
        ));
    }

    /**
     * Retourne les données d'un agent au format JSON.
     */
    public function showJson(User $user)
    {
        $user->load(['profil', 'ministere', 'regionChoices.region']);

        return response()->json([
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'matricule' => $user->matricule,
            'telephone' => $user->telephone,
            'email' => $user->email,
            'photo' => $user->photo,
            'ministere' => $user->ministere->nom ?? '—',
            'direction' => $user->direction ?? '—',
            'profil' => $user->profil->libelle ?? '—',
            'profil_id' => $user->profil_id,
            'experiences' => $user->experiences ?? [],
            'competences_techniques' => $user->competences_techniques ?? [],
            'disponibilite' => $user->disponibilite,
            'validation_status' => $user->validation_status,
            'region_choices' => $user->regionChoices->map(function($choice) {
                return [
                    'ordre' => $choice->ordre,
                    'region' => $choice->region->nom
                ];
            })
        ]);
    }

    /**
     * Retourne les stats dashboard.
     */
    public function stats()
    {
        // Métriques globales
        // IDs des utilisateurs complétés (ayant au moins 1 choix de région)
        $completedUserIds = UserRegionChoice::distinct('user_id')->pluck('user_id')->toArray();

        $totalCandidates = count($completedUserIds);
        $candidatesByProfil = User::selectRaw('profil_id, profil.libelle, COUNT(*) as total')
            ->leftJoin('profil', 'users.profil_id', '=', 'profil.id')
            ->whereIn('users.id', $completedUserIds)
            ->groupBy('profil_id', 'profil.libelle')
            ->orderByDesc('total')
            ->get();

        $candidatesByNiveau = User::selectRaw('niveau_numerique, COUNT(*) as total')
            ->whereNotNull('niveau_numerique')
            ->whereIn('users.id', $completedUserIds)
            ->groupBy('niveau_numerique')
            ->get()
            ->mapWithKeys(fn($item) => [$item->niveau_numerique => $item->total]);

        $candidatesByDisponibilite = User::selectRaw('disponibilite, COUNT(*) as total')
            ->whereNotNull('disponibilite')
            ->whereIn('users.id', $completedUserIds)
            ->groupBy('disponibilite')
            ->get()
            ->mapWithKeys(fn($item) => [$item->disponibilite => $item->total]);

        $candidatesByMinistere = User::selectRaw('ministere_id, ministeres.nom, COUNT(*) as total')
            ->leftJoin('ministeres', 'users.ministere_id', '=', 'ministeres.id')
            ->whereIn('users.id', $completedUserIds)
            ->groupBy('ministere_id', 'ministeres.nom')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Top régions choisies
        $topRegions = UserRegionChoice::selectRaw('region_id, region.nom, COUNT(*) as total')
            ->leftJoin('regions', 'user_region_choices.region_id', '=', 'regions.id')
            ->groupBy('region_id', 'region.nom')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Distribution par profil et niveau
        $profilNiveauStats = User::selectRaw('profil_id, profil.libelle, niveau_numerique, COUNT(*) as total')
            ->leftJoin('profil', 'users.profil_id', '=', 'profil.id')
            ->whereNotNull('niveau_numerique')
            ->whereIn('users.id', $completedUserIds)
            ->groupBy('profil_id', 'profil.libelle', 'niveau_numerique')
            ->get()
            ->groupBy('libelle');

        return [
            'totalCandidates' => $totalCandidates,
            'candidatesByProfil' => $candidatesByProfil,
            'candidatesByNiveau' => $candidatesByNiveau,
            'candidatesByDisponibilite' => $candidatesByDisponibilite,
            'candidatesByMinistere' => $candidatesByMinistere,
            'topRegions' => $topRegions,
            'profilNiveauStats' => $profilNiveauStats,
        ];
    }

    /**
     * Retourne stats d'un profil spécifique.
     */
    public function profilDetail(Profil $profil)
    {
        // IDs des utilisateurs complétés
        $completedUserIds = UserRegionChoice::distinct('user_id')->pluck('user_id')->toArray();

        $totalByProfil = User::whereIn('id', $completedUserIds)->where('profil_id', $profil->id)->count();

        $niveauStats = User::whereIn('id', $completedUserIds)->where('profil_id', $profil->id)
            ->selectRaw('niveau_numerique, COUNT(*) as total')
            ->groupBy('niveau_numerique')
            ->get()
            ->mapWithKeys(fn($item) => [$item->niveau_numerique => $item->total]);

        // Compétences techniques les plus courantes (filtrées aux users complétés)
        $competencesStats = User::whereIn('id', $completedUserIds)->where('profil_id', $profil->id)
            ->get()
            ->flatMap(function ($user) {
                return $user->competences_techniques ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        // Expériences les plus courantes (filtrées aux users complétés)
        $experiencesStats = User::whereIn('id', $completedUserIds)->where('profil_id', $profil->id)
            ->get()
            ->flatMap(function ($user) {
                return $user->experiences ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        $metierStats = User::whereIn('id', $completedUserIds)
            ->where('profil_id', $profil->id)
            ->selectRaw("COALESCE(NULLIF(TRIM(metier), ''), 'Non précisé') as metier_label, COUNT(*) as total")
            ->groupBy('metier_label')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $sourceStats = User::whereIn('id', $completedUserIds)
            ->where('profil_id', $profil->id)
            ->selectRaw("COALESCE(source_type, 'manual') as source_type, COUNT(*) as total")
            ->groupBy('source_type')
            ->orderByDesc('total')
            ->get();

        // Choix régionaux pour ce profil (seulement utilisateurs complétés)
        $profilUserIds = User::where('profil_id', $profil->id)->pluck('id')->toArray();
        $profilCompletedUserIds = array_values(array_intersect($profilUserIds, $completedUserIds));

        $regionalChoices = UserRegionChoice::selectRaw('region_id, region.nom, ordre, COUNT(*) as total')
            ->leftJoin('regions', 'user_region_choices.region_id', '=', 'regions.id')
            ->whereIn('user_id', $profilCompletedUserIds)
            ->groupBy('region_id', 'region.nom', 'ordre')
            ->orderByDesc('total')
            ->get();

        return view('admin.candidates.profil-detail', compact(
            'profil',
            'totalByProfil',
            'niveauStats',
            'competencesStats',
            'experiencesStats',
            'metierStats',
            'sourceStats',
            'regionalChoices'
        ));
    }

    /**
     * Met à jour les informations d'un agent (Statut, Téléphone, etc.)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'telephone' => ['nullable', 'digits:9', 'unique:users,telephone,' . $user->id],
            // On accepte Matricule (6 chiffres + 1 lettre) OU CIN (numérique 13-14 chiffres)
            'matricule' => ['nullable', 'unique:users,matricule,' . $user->id, 'regex:/^([0-9]{6}[A-Z]|[0-9]{10,20})$/'],
            'validation_status' => ['nullable', 'in:reserve,officiel,officiel_inscrit,officiel_attente'],
        ], [
            'matricule.unique' => 'Ce Matricule/CIN est déjà utilisé par un autre agent.',
            'matricule.regex' => 'Format invalide. Utilisez le matricule (ex: 123456A) ou le CIN (ex: 1765...)',
        ]);

        $data = [];
        
        if ($request->has('telephone')) {
            $data['telephone'] = $validated['telephone'];
        }

        if ($request->has('matricule')) {
            $data['matricule'] = strtoupper(trim($validated['matricule']));
        }

        if ($request->has('validation_status')) {
            $status = $validated['validation_status'];
            
            if ($status === 'officiel') {
                // Détection intelligente : Inscrit si au moins une info technique est présente
                $hasTechnicalInfo = (!empty($user->experiences) && $user->experiences != '[]') || !empty($user->niveau_numerique);
                $status = $hasTechnicalInfo ? 'officiel_inscrit' : 'officiel_attente';
            }
            
            $data['validation_status'] = $status;
            
            if (str_contains($status, 'officiel')) {
                $data['validation_source'] = $user->validation_source ?: 'Manuel';
            } else {
                $data['validation_source'] = null;
            }
        }

        $user->update($data);

        return back()->with('success', 'Informations mises à jour.');
    }

    /**
     * Supprime un candidat et ses dépendances liées.
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            // Supprimer les motivations liées aux choix régionaux
            foreach ($user->regionChoices as $choice) {
                $choice->userRegionChoiceMotivations()->delete();
            }

            // Supprimer les choix régionaux
            $user->regionChoices()->delete();

            // Supprimer les réponses dynamiques
            $user->dynamicAnswers()->delete();

            // Enfin supprimer l'utilisateur
            $user->delete();
        });

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidat supprimé avec succès.');
    }
}
