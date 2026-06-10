<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\Region;
use App\Models\Profil;
use App\Models\DeploymentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SimulationExport;

class AdminOperationsResearchController extends Controller
{
    /**
     * Profils métier pris en compte dans le déploiement.
     */
    private function deploymentProfiles(): array
    {
        $profileCodes = ['superviseur', 'chef_equipe', 'auditeur', 'auditeur_it', 'chauffeur'];
        $profilesByCode = Profil::whereIn('code', $profileCodes)->get()->keyBy('code');

        $definitions = [
            'superviseur' => [
                'label' => 'Superviseur',
                'icon' => 'bi-shield-shaded',
                'summary' => 'Superviseurs',
            ],
            'chef_equipe' => [
                'label' => "Chef d'équipe",
                'icon' => 'bi-person-badge',
                'summary' => 'Chefs',
            ],
            'auditeur' => [
                'label' => 'Auditeur',
                'icon' => 'bi-person',
                'summary' => 'Auditeurs',
            ],
            'auditeur_it' => [
                'label' => 'Support',
                'icon' => 'bi-tools',
                'summary' => 'Supports',
            ],
            'chauffeur' => [
                'label' => 'Chauffeur',
                'icon' => 'bi-car-front',
                'summary' => 'Chauffeurs',
            ],
        ];

        $profiles = [];

        foreach ($profileCodes as $code) {
            $profile = $profilesByCode->get($code);

            if ($profile) {
                $profiles[] = [
                    'id' => $profile->id,
                    'code' => $profile->code,
                    'label' => $definitions[$code]['label'],
                    'icon' => $definitions[$code]['icon'],
                    'summary' => $definitions[$code]['summary'],
                ];
            }
        }

        return $profiles;
    }

    /**
     * Retourne les libellés de profils utilisés dans le déploiement, indexés par ID.
     */
    private function deploymentRoleLabels(): array
    {
        return collect($this->deploymentProfiles())
            ->mapWithKeys(fn (array $profile) => [$profile['id'] => $profile['label']])
            ->all();
    }

    /**
     * Prépare le contexte commun de la page.
     */
    private function buildPageContext(Request $request): array
    {
        $selectedRegionId = $request->input('region_id');

        $regions = Region::orderBy('nom')->get();
        $profils = Profil::orderBy('ordre')->orderBy('libelle')->get();

        $teamsQuery = Team::with([
            'members' => function ($query) {
                $query->with([
                    'profil',
                    'ministere',
                    'regionChoices' => function ($regionQuery) {
                        $regionQuery->orderBy('ordre');
                    },
                    'regionChoices.region',
                ]);
            },
            'region',
        ]);

        if ($selectedRegionId) {
            $teamsQuery->where('region_id', $selectedRegionId);
        }

        $teams = $teamsQuery->get();

        $unassignedUsersQuery = User::with([
            'profil',
            'ministere',
            'regionChoices' => function ($query) {
                $query->orderBy('ordre');
            },
            'regionChoices.region',
        ])->whereNull('team_id');

        if ($selectedRegionId) {
            $unassignedUsersQuery->whereHas('regionChoices', function ($q) use ($selectedRegionId) {
                $q->where('region_id', $selectedRegionId)->where('ordre', 1);
            });
        }

        $unassignedUsers = $unassignedUsersQuery->get();

        // On calcule le stock disponible par profil pour le moteur d'aide à la décision
        $availablePoolCounts = $unassignedUsers->groupBy('profil_id')->map->count();

        return compact('teams', 'regions', 'profils', 'unassignedUsers', 'selectedRegionId', 'availablePoolCounts');
    }

    /**
     * Affiche l'interface de gestion des équipes.
     */
    public function index(Request $request)
    {
        return view('admin.operations-research', $this->buildPageContext($request) + [
            'deploymentBlocks' => $this->defaultDeploymentBlocks(),
            'deploymentProfiles' => $this->deploymentProfiles(),
        ]);
    }

    /**
     * Blocs de déploiement par défaut.
     */
    private function defaultDeploymentBlocks(): array
    {
        $quotas = [];
        foreach ($this->deploymentProfiles() as $profile) {
            $quotas[$profile['id']] = 1;
        }

        return [
            [
                'team_count' => 3,
                'team_size' => 5,
                'quotas' => $quotas,
            ],
        ];
    }

    /**
     * Récupère les candidats libres regroupés par profil.
     */
    private function availablePools(): array
    {
        $usersByProfile = User::with(['profil', 'ministere', 'regionChoices.region'])
            ->whereNull('team_id')
            ->get()
            ->groupBy('profil_id');

        $pools = [];

        foreach ($this->deploymentProfiles() as $profile) {
            $pools[$profile['id']] = $usersByProfile->get($profile['id'], collect())->values();
        }

        return $pools;
    }

    /**
     * Normalise les blocs saisis par l'utilisateur.
     */
    private function resolveDeploymentBlocks(Request $request): array
    {
        $profiles = $this->deploymentProfiles();
        $rawBlocks = $request->input('deployment_blocks');

        if (is_array($rawBlocks) && !empty($rawBlocks)) {
            $normalizedBlocks = [];

            foreach ($rawBlocks as $block) {
                $teamCount = (int) ($block['team_count'] ?? 0);

                if ($teamCount > 0) {
                    $quotas = [];
                    $teamSize = 0;

                    foreach ($profiles as $profile) {
                        $q = max(0, (int) ($block['quotas'][$profile['id']] ?? 0));
                        $quotas[$profile['id']] = $q;
                        $teamSize += $q;
                    }

                    if ($teamSize > 0) {
                        $normalizedBlocks[] = [
                            'team_count' => $teamCount,
                            'team_size' => $teamSize,
                            'quotas' => $quotas,
                        ];
                    }
                }
            }

            if (!empty($normalizedBlocks)) {
                return $normalizedBlocks;
            }
        }

        $teamCount = (int) $request->input('team_count', 1);
        $quotas = [];
        $ts = 0;
        foreach ($profiles as $profile) {
            $q = 1; 
            $quotas[$profile['id']] = $q;
            $ts += $q;
        }

        return [
            [
                'team_count' => max(1, $teamCount),
                'team_size' => $ts,
                'quotas' => $quotas,
            ],
        ];
    }

    /**
     * Vérifie les blocs de déploiement.
     */
    private function validateDeploymentBlocks(array $blocks): array
    {
        $profiles = $this->deploymentProfiles();

        if (empty($blocks)) {
            throw ValidationException::withMessages([
                'deployment_blocks' => 'Ajoutez au moins un bloc de répartition.',
            ]);
        }

        if (empty($profiles)) {
            throw ValidationException::withMessages([
                'deployment_blocks' => 'Aucun profil de déploiement disponible.',
            ]);
        }

        foreach ($blocks as $index => $block) {
            if ($block['team_count'] < 1) {
                throw ValidationException::withMessages([
                    "deployment_blocks.$index.team_count" => 'Le nombre d’équipes doit être au moins 1.',
                ]);
            }

            $quotas = $block['quotas'] ?? [];
            $sum = 0;

            foreach ($profiles as $profile) {
                $pid = $profile['id'];
                $q = isset($quotas[$pid]) ? (int) $quotas[$pid] : 0;

                if ($q < 0) {
                    throw ValidationException::withMessages([
                        "deployment_blocks.$index.quotas.$pid" => 'Les quotas doivent être des nombres entiers positifs.',
                    ]);
                }

                $sum += $q;
            }

            if ($sum < 1) {
                throw ValidationException::withMessages([
                    "deployment_blocks.$index" => 'La somme des quotas par bloc doit être au moins de 1 agent.',
                ]);
            }
        }

        return $blocks;
    }

    /**
     * Transforme un utilisateur en membre de déploiement.
     */
    private function makeDeploymentMember(User $user, string $roleLabel): array
    {
        return [
            'id' => $user->id,
            'profil_id' => $user->profil_id,
            'profil_code' => $user->profil?->code,
            'role' => $roleLabel,
            'name' => trim($user->prenom . ' ' . $user->nom),
            'profil' => $user->profil?->libelle,
            'matricule' => $user->matricule ?? '',
            'telephone' => $user->telephone ?? '',
            'structure' => $user->ministere?->nom ?? '',
        ];
    }

    /**
     * Construit des équipes à partir de blocs définis par l'utilisateur.
     */
    private function buildPlanFromBlocks(array $blocks): array
    {
        $profiles = $this->deploymentProfiles();

        if (empty($profiles)) {
            return [
                'simulationTeams' => [],
                'simulationSummary' => [
                    'teams' => 0,
                    'requestedTotal' => 0,
                    'assignedTotal' => 0,
                    'missingTotal' => 0,
                    'mode' => 'blocks',
                ],
            ];
        }

        $pools = $this->availablePools();
        $roleLabels = $this->deploymentRoleLabels();
        $simulationTeams = [];
        $teamIndex = 1;
        $requestedTotal = 0;
        $availableCounts = [];
        $requestedCounts = [];

        foreach ($profiles as $profile) {
            $availableCounts[$profile['id']] = $pools[$profile['id']]->count();
            $requestedCounts[$profile['id']] = 0;
        }

        foreach ($blocks as $block) {
            $quotas = $block['quotas'] ?? [];

            for ($i = 0; $i < $block['team_count']; $i++) {
                $members = [];

                // Respect per-profile quotas strictly
                foreach ($profiles as $profile) {
                    $profilId = $profile['id'];
                    $want = isset($quotas[$profilId]) ? (int) $quotas[$profilId] : 0;
                    
                    for ($r = 0; $r < $want; $r++) {
                        if ($pools[$profilId]->isNotEmpty()) {
                            // On cherche un agent dont le ministère n'est pas encore représenté dans l'équipe
                            $currentMinistereIds = collect($members)->pluck('ministere_id')->filter()->all();
                            
                            $foundIndex = $pools[$profilId]->search(function($u) use ($currentMinistereIds) {
                                // On accepte si c'est un nouveau ministère ou si l'agent n'a pas de ministère renseigné
                                return $u->ministere_id === null || !in_array($u->ministere_id, $currentMinistereIds);
                            });

                            if ($foundIndex !== false) {
                                // On tire cet agent précis de la collection
                                $user = $pools[$profilId]->pull($foundIndex);
                            } else {
                                // Fallback: on prend le premier disponible (plus de diversité possible)
                                $user = $pools[$profilId]->shift();
                            }
                            
                            $members[] = $this->makeDeploymentMember($user, $roleLabels[$profilId]);
                        }
                    }
                    // Count requested slots even if not available
                    $requestedCounts[$profilId] += $want;
                }

                $simulationTeams[] = [
                    'nom' => 'Équipe ' . $teamIndex++,
                    'members' => $members,
                    'target_size' => $block['team_size'],
                ];

                $requestedTotal += $block['team_size'];
            }
        }

        $assignedTotals = array_fill_keys(array_column($profiles, 'id'), 0);
        foreach ($simulationTeams as $team) {
            foreach ($team['members'] as $member) {
                if (!empty($member['profil_id']) && array_key_exists($member['profil_id'], $assignedTotals)) {
                    $assignedTotals[$member['profil_id']]++;
                }
            }
        }

        $summaryMap = [
            'superviseur' => ['requested' => 'requestedSuperviseurs', 'available' => 'availableSuperviseurs', 'assigned' => 'assignedSuperviseurs'],
            'chef_equipe' => ['requested' => 'requestedChefs', 'available' => 'availableChefs', 'assigned' => 'assignedChefs'],
            'auditeur' => ['requested' => 'requestedAuditeurs', 'available' => 'availableAuditeurs', 'assigned' => 'assignedAuditeurs'],
            'auditeur_it' => ['requested' => 'requestedSupports', 'available' => 'availableSupports', 'assigned' => 'assignedSupports'],
            'chauffeur' => ['requested' => 'requestedChauffeurs', 'available' => 'availableChauffeurs', 'assigned' => 'assignedChauffeurs'],
        ];

        $summary = [
            'teams' => count($simulationTeams),
            'requestedTotal' => $requestedTotal,
            'assignedTotal' => array_sum($assignedTotals),
            'missingTotal' => max(0, $requestedTotal - array_sum($assignedTotals)),
            'mode' => 'blocks',
        ];

        foreach ($profiles as $profile) {
            $map = $summaryMap[$profile['code']] ?? null;

            if ($map) {
                $summary[$map['requested']] = $requestedCounts[$profile['id']] ?? 0;
                $summary[$map['available']] = $availableCounts[$profile['id']] ?? 0;
                $summary[$map['assigned']] = $assignedTotals[$profile['id']] ?? 0;
            }
        }

        return [
            'simulationTeams' => $simulationTeams,
            'simulationSummary' => $summary,
        ];
    }

    /**
     * Simule une répartition sans écrire en base.
     */
    public function simulateDistribute(Request $request)
    {
        $blocks = $this->validateDeploymentBlocks($this->resolveDeploymentBlocks($request));
        $pageContext = $this->buildPageContext($request);
        
        $pageContext['teams'] = collect();

        $deploymentPlan = $this->buildPlanFromBlocks($blocks);

        $pageContext = $this->filterSimulatedUsers($pageContext, $deploymentPlan);

        return view('admin.operations-research', $pageContext + $deploymentPlan + [
            'deploymentBlocks' => $blocks,
            'deploymentProfiles' => $this->deploymentProfiles(),
        ]);
    }

    /**
     * Exporte la simulation en cours au format Excel.
     */
    public function exportSimulation(Request $request)
    {
        // On vérifie si un état manuel (Drag & Drop) a été envoyé depuis le JS
        $manualState = $request->input('simulation_state');

        if (!empty($manualState)) {
            $decodedState = json_decode($manualState, true);
            
            if (is_array($decodedState)) {
                $simulationTeams = [];
                $roleLabels = $this->deploymentRoleLabels();

                foreach ($decodedState as $teamData) {
                    $userIds = $teamData['user_ids'] ?? [];
                    
                    // On recharge les membres complets depuis la DB pour avoir matricule, tel, etc.
                    $users = User::with(['profil', 'ministere'])->whereIn('id', $userIds)->get()->keyBy('id');
                    
                    $members = [];
                    foreach ($userIds as $uid) {
                        if ($user = $users->get($uid)) {
                            $members[] = $this->makeDeploymentMember($user, $roleLabels[$user->profil_id] ?? 'Agent');
                        }
                    }

                    $simulationTeams[] = [
                        'nom' => $teamData['nom'] ?? 'Équipe',
                        'members' => $members,
                    ];
                }

                $filename = 'simulation_deploiement_manuelle_' . now()->format('Ymd_His') . '.xlsx';
                return Excel::download(new SimulationExport($simulationTeams), $filename);
            }
        }

        // Sinon, on retombe sur la logique par blocs classique
        $blocks = $this->validateDeploymentBlocks($this->resolveDeploymentBlocks($request));
        $deploymentPlan = $this->buildPlanFromBlocks($blocks);

        $filename = 'simulation_deploiement_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SimulationExport($deploymentPlan['simulationTeams']), $filename);
    }

    /**
     * Filtre les utilisateurs déjà assignés dans une simulation.
     */
    private function filterSimulatedUsers(array $pageContext, array $deploymentPlan): array
    {
        $simulatedUserIds = collect($deploymentPlan['simulationTeams'])
            ->pluck('members')
            ->flatten(1)
            ->pluck('id')
            ->toArray();

        $pageContext['unassignedUsers'] = $pageContext['unassignedUsers']->filter(function ($user) use ($simulatedUserIds) {
            return !in_array($user->id, $simulatedUserIds);
        });

        return $pageContext;
    }

    /**
     * Prévisualise le déploiement.
     */
    public function optimizeDistribute(Request $request)
    {
        $blocks = $this->validateDeploymentBlocks($this->resolveDeploymentBlocks($request));
        $pageContext = $this->buildPageContext($request);

        $pageContext['teams'] = collect();

        $deploymentPlan = $this->buildPlanFromBlocks($blocks);

        $pageContext = $this->filterSimulatedUsers($pageContext, $deploymentPlan);

        return view('admin.operations-research', $pageContext + $deploymentPlan + [
            'deploymentBlocks' => $blocks,
            'deploymentProfiles' => $this->deploymentProfiles(),
            'deploymentMode' => 'auto',
        ]);
    }

    /**
     * Crée une nouvelle équipe.
     */
    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
        ]);

        Team::create($validated);

        return back()->with('success', 'Équipe créée avec succès.');
    }

    /**
     * Assigne un membre à une équipe.
     */
    public function assignMember(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (!empty($validated['team_id'])) {
            $targetTeamId = (int) $validated['team_id'];

            if ((int) $user->team_id !== $targetTeamId) {
                $hasSameProfileInTeam = User::where('team_id', $targetTeamId)
                    ->where('profil_id', $user->profil_id)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($hasSameProfileInTeam) {
                    return back()->withErrors([
                        'assign' => 'Cette équipe a déjà un agent avec ce profil.',
                    ]);
                }
            }
        }

        $user->team_id = $validated['team_id'];
        $user->save();

        return back()->with('success', 'Membre mis à jour.');
    }

    /**
     * Modifie le profil d'un agent.
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'profil_id' => 'required|exists:profil,id',
            'direction' => 'nullable|string|max:255',
            'ministere_id' => 'nullable|exists:ministeres,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $newProfilId = (int) $validated['profil_id'];

        if (empty($user->profil_initial_id)) {
            $user->profil_initial_id = $user->profil_id;
        }

        if (!empty($user->team_id)) {
            $teamHasProfile = User::where('team_id', $user->team_id)
                ->where('profil_id', $newProfilId)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($teamHasProfile) {
                return back()->withErrors([
                    'profile' => 'Cette équipe contient déjà un agent avec ce profil.',
                ]);
            }
        }

        $user->profil_id = $newProfilId;
        if (array_key_exists('direction', $validated)) {
            $user->direction = trim((string) $validated['direction']);
        }
        if (array_key_exists('ministere_id', $validated)) {
            $user->ministere_id = $validated['ministere_id'] ?: null;
        }
        $user->save();

        return back()->with('success', 'Profil de l’agent mis à jour.');
    }

    /**
     * Algorithme de répartition automatique flexible.
     */
    public function autoDistribute(Request $request)
    {
        $blocks = $this->validateDeploymentBlocks($this->resolveDeploymentBlocks($request));
        $deploymentPlan = $this->buildPlanFromBlocks($blocks);

        DB::transaction(function () use ($deploymentPlan) {
            foreach ($deploymentPlan['simulationTeams'] as $teamPlan) {
                $team = Team::create([
                    'nom' => $teamPlan['nom'],
                    'region_id' => null,
                ]);

                foreach ($teamPlan['members'] as $member) {
                    User::whereKey($member['id'])->update([
                        'team_id' => $team->id,
                    ]);
                }
            }
        });

        $summary = $deploymentPlan['simulationSummary'];

        if ($summary['missingTotal'] > 0) {
            return back()->with('info', sprintf(
                'Déploiement appliqué: %d équipe(s) créée(s), %d place(s) non pourvue(s) sur %d demandées.',
                $summary['teams'],
                $summary['missingTotal'],
                $summary['requestedTotal']
            ));
        }

        return back()->with('success', sprintf(
            'Déploiement appliqué: %d équipe(s) créée(s) avec %d agent(s).',
            $summary['teams'],
            $summary['assignedTotal']
        ));
    }

    /**
     * Permute deux membres d'équipe.
     */
    public function swapMembers(Request $request)
    {
        $validated = $request->validate([
            'user1_id' => 'required|exists:users,id',
            'user2_id' => 'required|exists:users,id',
        ]);

        $user1 = User::findOrFail($validated['user1_id']);
        $user2 = User::findOrFail($validated['user2_id']);

        DB::transaction(function () use ($user1, $user2) {
            $team1Id = $user1->team_id;
            $team2Id = $user2->team_id;
            $profil1Id = $user1->profil_id;
            $profil2Id = $user2->profil_id;

            // Échange complet de position (Équipe + Profil)
            // Fonctionne même si l'un des deux (ou les deux) n'a pas d'équipe (null)
            $user1->team_id = $team2Id;
            $user1->profil_id = $profil2Id;
            $user2->team_id = $team1Id;
            $user2->profil_id = $profil1Id;

            $user1->save();
            $user2->save();
        });

        return back()->with('success', sprintf(
            'Échange réussi entre %s %s et %s %s.',
            $user1->prenom, $user1->nom,
            $user2->prenom, $user2->nom
        ));
    }

    /**
     * Réinitialise complètement le déploiement.
     */
    public function resetDeployment()
    {
        DB::transaction(function () {
            User::query()->update(['team_id' => null]);
            Team::query()->delete();
        });

        return back()->with('success', 'Déploiement réinitialisé. Tous les agents sont à nouveau sans équipe.');
    }

    /**
     * Enregistre un plan de déploiement (scénario) en base de données.
     */
    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'simulation_state' => 'required|json',
        ]);

        $decodedState = json_decode($validated['simulation_state'], true);
        
        // Calcul du résumé pour la bibliothèque
        $nbTeams = count($decodedState);
        $nbMembers = collect($decodedState)->pluck('user_ids')->flatten()->count();
        
        // On récupère les quotas d'origine depuis les blocs pour le metadata
        $blocks = $this->resolveDeploymentBlocks($request);

        DeploymentPlan::create([
            'nom' => $validated['nom'],
            'data' => $decodedState,
            'summary' => [
                'teams_count' => $nbTeams,
                'members_count' => $nbMembers,
                'created_at_human' => now()->format('d/m/Y H:i'),
            ],
            'metadata' => [
                'blocks' => $blocks,
                'region_id' => $request->input('region_id'),
            ],
        ]);

        return back()->with('success', 'Plan de déploiement "' . $validated['nom'] . '" enregistré avec succès.');
    }

    /**
     * Supprime une équipe et libère ses membres.
     */
    public function destroyTeam(Team $team)
    {
        DB::transaction(function() use ($team) {
            User::where('team_id', $team->id)->update(['team_id' => null]);
            $team->delete();
        });

        return back()->with('success', 'Équipe supprimée et membres libérés.');
    }
}
