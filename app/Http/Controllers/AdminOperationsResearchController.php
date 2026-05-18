<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\Region;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOperationsResearchController extends Controller
{
    /**
     * Affiche l'interface de gestion des équipes.
     */
    public function index(Request $request)
    {
        $selectedRegionId = $request->input('region_id');
        
        $regions = Region::orderBy('nom')->get();
        
        // On récupère les équipes de la région sélectionnée (ou toutes si aucune)
        $teamsQuery = Team::with(['members.profil', 'region']);
        if ($selectedRegionId) {
            $teamsQuery->where('region_id', $selectedRegionId);
        }
        $teams = $teamsQuery->get();

        // On récupère les candidats non assignés
        $unassignedUsersQuery = User::with(['profil', 'regionChoices.region'])
            ->whereNull('team_id');
            
        if ($selectedRegionId) {
            // Optionnel: filtrer les candidats qui ont choisi cette région en choix 1
            $unassignedUsersQuery->whereHas('regionChoices', function($q) use ($selectedRegionId) {
                $q->where('region_id', $selectedRegionId)->where('ordre', 1);
            });
        }
        
        $unassignedUsers = $unassignedUsersQuery->get();

        return view('admin.operations-research', compact('teams', 'regions', 'unassignedUsers', 'selectedRegionId'));
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
        $user->team_id = $validated['team_id'];
        $user->save();

        return back()->with('success', 'Membre mis à jour.');
    }

    /**
     * Algorithme de répartition automatique flexible.
     * Crée des équipes basées sur le nombre d'auditeurs disponibles.
     */
    public function autoDistribute(Request $request)
    {
        // 1. Récupérer tous les candidats non assignés
        $candidates = User::whereNull('team_id')->get()->groupBy('profil_id');

        $chefId = 1;
        $auditeurId = 2;
        $supportId = 3;

        // On récupère les listes (en convertissant en array pour utiliser array_shift ou indexation)
        $chefs = $candidates->get($chefId, collect())->all();
        $auditeurs = $candidates->get($auditeurId, collect())->all();
        $supports = $candidates->get($supportId, collect())->all();

        // On décide de créer des équipes basées sur le nombre d'auditeurs (le profil principal)
        // S'il n'y a pas d'auditeurs, on ne crée rien.
        $nbTeamsToCreate = count($auditeurs);

        if ($nbTeamsToCreate === 0) {
            return back()->with('info', 'Aucun auditeur disponible pour créer des équipes.');
        }

        DB::transaction(function () use ($nbTeamsToCreate, $chefs, $auditeurs, $supports) {
            for ($i = 0; $i < $nbTeamsToCreate; $i++) {
                $teamCount = Team::count() + 1;
                $team = Team::create([
                    'nom' => "Équipe #{$teamCount}",
                    'region_id' => null, 
                ]);

                // On assigne l'auditeur (obligatoire ici car c'est notre base)
                $auditeur = $auditeurs[$i];
                $auditeur->team_id = $team->id;
                $auditeur->save();

                // On assigne un chef s'il en reste
                if (isset($chefs[$i])) {
                    $chefs[$i]->team_id = $team->id;
                    $chefs[$i]->save();
                }

                // On assigne un support s'il en reste
                if (isset($supports[$i])) {
                    $supports[$i]->team_id = $team->id;
                    $supports[$i]->save();
                }
            }
        });

        return back()->with('success', "{$nbTeamsToCreate} équipes créées basées sur les auditeurs disponibles.");
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
