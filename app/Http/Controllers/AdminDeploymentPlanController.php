<?php

namespace App\Http\Controllers;

use App\Models\DeploymentPlan;
use App\Models\User;
use App\Models\Profil;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SimulationExport;

class AdminDeploymentPlanController extends Controller
{
    /**
     * Liste des plans de déploiement sauvegardés.
     */
    public function index()
    {
        $plans = DeploymentPlan::where(function ($query) {
                $query->whereNull('is_draft')->orWhere('is_draft', false);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.deployment_plans.index', compact('plans'));
    }

    /**
     * Recharge un plan sauvegarde comme brouillon de travail.
     */
    public function resume(DeploymentPlan $plan)
    {
        if ($plan->is_draft) {
            return redirect()
                ->route('admin.operations.research')
                ->with('info', 'Ce brouillon est deja actif.');
        }

        $data = $plan->data ?? [];
        $metadata = $plan->metadata ?? [];
        $summary = $plan->summary ?? [];

        $teamCount = count($data);
        $memberCount = collect($data)->pluck('user_ids')->flatten()->count();

        DeploymentPlan::updateOrCreate(
            ['is_draft' => true],
            [
                'nom' => 'Brouillon - ' . $plan->nom,
                'data' => $data,
                'summary' => array_merge($summary, [
                    'teams_count' => $teamCount,
                    'members_count' => $memberCount,
                    'updated_at_human' => now()->format('d/m/Y H:i'),
                ]),
                'metadata' => array_merge($metadata, [
                    'source_plan_id' => $plan->id,
                    'source_plan_name' => $plan->nom,
                    'resumed_at' => now()->toDateTimeString(),
                ]),
            ]
        );

        return redirect()
            ->route('admin.operations.research', [
                'region_id' => $metadata['region_id'] ?? null,
            ])
            ->with('success', 'Plan "' . $plan->nom . '" repris comme brouillon de simulation.');
    }

    /**
     * Supprime un plan de déploiement.
     */
    public function destroy(DeploymentPlan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan de déploiement supprimé.');
    }

    /**
     * Télécharge l'export Excel d'un plan sauvegardé.
     */
    public function download(DeploymentPlan $plan)
    {
        $savedData = $plan->data; // [{nom: "Equipe 1", user_ids: [1,2,3]}]
        $hydratedTeams = [];

        // On récupère tous les IDs uniques du plan pour faire une seule requête
        $allUserIds = collect($savedData)->pluck('user_ids')->flatten()->unique()->toArray();
        $users = User::with(['profil', 'ministere'])->whereIn('id', $allUserIds)->get()->keyBy('id');
        
        // Étiquettes de rôles (fallback)
        $profils = Profil::all()->keyBy('id');

        foreach ($savedData as $teamData) {
            $members = [];
            foreach ($teamData['user_ids'] ?? [] as $uid) {
                if ($user = $users->get($uid)) {
                    $members[] = [
                        'name' => trim($user->prenom . ' ' . $user->nom),
                        'profil' => $user->profil?->libelle ?? 'Inconnu',
                        'role' => $user->profil?->libelle ?? 'Agent',
                        'matricule' => $user->matricule ?? '',
                        'telephone' => $user->telephone ?? '',
                        'structure' => $user->ministere?->nom ?? '',
                    ];
                } else {
                    $members[] = [
                        'name' => 'Agent retiré du vivier (ID #' . $uid . ')',
                        'profil' => '-',
                        'role' => '-',
                        'matricule' => '-',
                        'telephone' => '-',
                        'structure' => '-',
                    ];
                }
            }

            $hydratedTeams[] = [
                'nom' => $teamData['nom'] ?? 'Équipe',
                'members' => $members,
            ];
        }

        $filename = 'export_plan_' . str($plan->nom)->slug('_') . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new SimulationExport($hydratedTeams), $filename);
    }
}
