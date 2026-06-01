<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\Region;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AdminOperationsResearchController extends Controller
{
    /**
     * Profils métier pris en compte dans le déploiement.
     */
    private function deploymentProfiles(): array
    {
        $profileCodes = ['chef_equipe', 'auditeur', 'auditeur_it', 'chauffeur'];
        $profilesByCode = Profil::whereIn('code', $profileCodes)->get()->keyBy('code');

        $definitions = [
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

        return compact('teams', 'regions', 'profils', 'unassignedUsers', 'selectedRegionId');
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
                $teamSize = (int) ($block['team_size'] ?? 0);

                if ($teamCount > 0 && $teamSize > 0) {
                    $quotas = [];

                    foreach ($profiles as $index => $profile) {
                        $fallbackQuota = $index === count($profiles) - 1
                            ? max(0, $teamSize - max(0, count($profiles) - 1))
                            : 1;
                        $quotas[$profile['id']] = max(0, (int) ($block['quotas'][$profile['id']] ?? $fallbackQuota));
                    }

                    $normalizedBlocks[] = [
                        'team_count' => $teamCount,
                        'team_size' => $teamSize,
                        'quotas' => $quotas,
                    ];
                }
            }

            if (!empty($normalizedBlocks)) {
                return $normalizedBlocks;
            }
        }

        $teamCount = (int) $request->input('team_count', $this->defaultDeploymentBlocks()[0]['team_count']);
        $teamSize = (int) $request->input('team_size', $this->defaultDeploymentBlocks()[0]['team_size']);

        $tc = max(1, $teamCount);
        $ts = max(3, $teamSize);
        $quotas = [];
        foreach ($profiles as $index => $profile) {
            $quotas[$profile['id']] = $index === count($profiles) - 1
                ? max(0, $ts - max(0, count($profiles) - 1))
                : 1;
        }

        return [
            [
                'team_count' => $tc,
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

            if ($block['team_size'] < 3) {
                throw ValidationException::withMessages([
                    "deployment_blocks.$index.team_size" => 'Une équipe doit avoir au moins 3 membres.',
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

                if ($q > $block['team_size']) {
                    throw ValidationException::withMessages([
                        "deployment_blocks.$index.quotas.$pid" => 'Un quota par profil ne peut pas dépasser la taille d\'équipe.',
                    ]);
                }

                $sum += $q;
            }

            if ($sum > $block['team_size']) {
                throw ValidationException::withMessages([
                    "deployment_blocks.$index" => 'La somme des quotas par profil ne peut pas dépasser la taille d\'équipe.',
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
        ];
    }

    /**
     * Choisit le profil disponible le plus abondant pour remplir un slot.
     */
    private function nextProfileToFill(array $pools): ?int
    {
        $ordered = collect(array_keys($pools))->sortByDesc(function ($profilId) use ($pools) {
            return $pools[$profilId]->count();
        })->values();

        foreach ($ordered as $profilId) {
            if ($pools[$profilId]->isNotEmpty()) {
                return $profilId;
            }
        }

        return null;
    }

    /**
     * Choisit l'équipe la moins remplie pour recevoir un membre supplémentaire.
     */
    private function nextTeamToFill(array $simulationTeams): ?int
    {
        $candidateIndex = null;
        $candidateSize = null;

        foreach ($simulationTeams as $index => $team) {
            $size = count($team['members']);

            if ($candidateIndex === null || $size < $candidateSize) {
                $candidateIndex = $index;
                $candidateSize = $size;
            }
        }

        return $candidateIndex;
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

                // Respect per-profile quotas first
                foreach ($profiles as $profile) {
                    $profilId = $profile['id'];
                    $want = isset($quotas[$profilId]) ? (int) $quotas[$profilId] : 0;
                    for ($r = 0; $r < $want; $r++) {
                        if ($pools[$profilId]->isNotEmpty()) {
                            $user = $pools[$profilId]->shift();
                            $members[] = $this->makeDeploymentMember($user, $roleLabels[$profilId]);
                        } else {
                            break;
                        }
                    }
                    // Count requested slots even if not available
                    $requestedCounts[$profilId] += $want;
                }

                // Fill remaining slots with most abundant profiles
                while (count($members) < $block['team_size']) {
                    $nextProfilId = $this->nextProfileToFill($pools);

                    if ($nextProfilId === null) {
                        break;
                    }

                    $user = $pools[$nextProfilId]->shift();
                    $members[] = $this->makeDeploymentMember($user, $roleLabels[$nextProfilId]);
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
     * Construit un plan automatique à partir des données disponibles.
     */
    private function buildOptimalPlan(): array
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
                    'mode' => 'auto',
                ],
            ];
        }

        $pools = $this->availablePools();
        $roleLabels = $this->deploymentRoleLabels();

        $availableCounts = [];

        foreach ($profiles as $profile) {
            $availableCounts[$profile['id']] = $pools[$profile['id']]->count();
        }

        $teamCount = min($availableCounts);

        if ($teamCount < 1) {
            $summary = [
                'teams' => 0,
                'requestedTotal' => 0,
                'assignedTotal' => 0,
                'missingTotal' => 0,
                'mode' => 'auto',
            ];

            $summaryMap = [
                'chef_equipe' => ['requested' => 'requestedChefs', 'available' => 'availableChefs', 'assigned' => 'assignedChefs'],
                'auditeur' => ['requested' => 'requestedAuditeurs', 'available' => 'availableAuditeurs', 'assigned' => 'assignedAuditeurs'],
                'auditeur_it' => ['requested' => 'requestedSupports', 'available' => 'availableSupports', 'assigned' => 'assignedSupports'],
                'chauffeur' => ['requested' => 'requestedChauffeurs', 'available' => 'availableChauffeurs', 'assigned' => 'assignedChauffeurs'],
            ];

            foreach ($profiles as $profile) {
                $map = $summaryMap[$profile['code']] ?? null;

                if ($map) {
                    $summary[$map['requested']] = 0;
                    $summary[$map['available']] = $availableCounts[$profile['id']] ?? 0;
                    $summary[$map['assigned']] = 0;
                }
            }

            return [
                'simulationTeams' => [],
                'simulationSummary' => $summary,
            ];
        }

        $simulationTeams = [];
        for ($i = 0; $i < $teamCount; $i++) {
            $simulationTeams[] = [
                'nom' => 'Équipe ' . ($i + 1),
                'members' => [],
                'target_size' => null,
            ];
        }

        // Base équilibrée: un membre de chaque profil par équipe quand c'est possible.
        foreach ($simulationTeams as &$team) {
            foreach ($profiles as $profile) {
                $profilId = $profile['id'];

                if ($pools[$profilId]->isNotEmpty()) {
                    $user = $pools[$profilId]->shift();
                    $team['members'][] = $this->makeDeploymentMember($user, $roleLabels[$profilId]);
                }
            }
        }
        unset($team);

        $totalAvailable = array_sum($availableCounts);
        $targetTeamSize = max(3, (int) ceil($totalAvailable / $teamCount));

        // On remplit ensuite les équipes les moins chargées avec les profils les plus disponibles.
        while (true) {
            $teamIndex = $this->nextTeamToFill($simulationTeams);
            $nextProfilId = $this->nextProfileToFill($pools);

            if ($teamIndex === null || $nextProfilId === null) {
                break;
            }

            $currentSize = count($simulationTeams[$teamIndex]['members']);

            if ($currentSize >= $targetTeamSize) {
                $isAnyTeamBelowTarget = collect($simulationTeams)->contains(function ($team) use ($targetTeamSize) {
                    return count($team['members']) < $targetTeamSize;
                });

                if (!$isAnyTeamBelowTarget) {
                    $targetTeamSize++;
                }
            }

            $user = $pools[$nextProfilId]->shift();
            $simulationTeams[$teamIndex]['members'][] = $this->makeDeploymentMember($user, $roleLabels[$nextProfilId]);
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
            'chef_equipe' => ['requested' => 'requestedChefs', 'available' => 'availableChefs', 'assigned' => 'assignedChefs'],
            'auditeur' => ['requested' => 'requestedAuditeurs', 'available' => 'availableAuditeurs', 'assigned' => 'assignedAuditeurs'],
            'auditeur_it' => ['requested' => 'requestedSupports', 'available' => 'availableSupports', 'assigned' => 'assignedSupports'],
            'chauffeur' => ['requested' => 'requestedChauffeurs', 'available' => 'availableChauffeurs', 'assigned' => 'assignedChauffeurs'],
        ];

        $summary = [
            'teams' => count($simulationTeams),
            'requestedTotal' => array_sum(array_map(fn ($team) => count($team['members']), $simulationTeams)),
            'assignedTotal' => array_sum($assignedTotals),
            'missingTotal' => max(0, array_sum($availableCounts) - array_sum($assignedTotals)),
            'mode' => 'auto',
        ];

        foreach ($profiles as $profile) {
            $map = $summaryMap[$profile['code']] ?? null;

            if ($map) {
                $summary[$map['requested']] = $availableCounts[$profile['id']] ?? 0;
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
        $deploymentPlan = $this->buildPlanFromBlocks($blocks);

        return view('admin.operations-research', $pageContext + $deploymentPlan + [
            'deploymentBlocks' => $blocks,
            'deploymentProfiles' => $this->deploymentProfiles(),
        ]);
    }

    /**
     * Prévisualise le meilleur déploiement possible sans paramètres saisis.
     */
    public function optimizeDistribute(Request $request)
    {
        $blocks = $this->validateDeploymentBlocks($this->resolveDeploymentBlocks($request));
        $pageContext = $this->buildPageContext($request);
        $deploymentPlan = $this->buildPlanFromBlocks($blocks);

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

        // Empêche les doublons de profil dans une même équipe.
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
     * Crée des équipes basées sur le nombre d'auditeurs disponibles.
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
     * Permute deux membres d'équipe ou déplace un membre vers une position occupée.
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

            // Permutation
            $user1->team_id = $team2Id;
            $user2->team_id = $team1Id;

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
     * Vide toutes les affectations et supprime les équipes existantes.
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
