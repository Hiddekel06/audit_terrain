<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profil;
use App\Models\Ministere;
use App\Models\Region;
use App\Models\UserRegionChoice;
use Illuminate\Support\Facades\DB;

class AdminCandidateController extends Controller
{
    /**
     * Affiche la liste de tous les candidats avec filtres.
     */
    public function index(Request $request)
    {
        $query = User::with(['profil', 'ministere', 'regionChoices.region']);

        // Filtres
        if ($request->filled('profil_id')) {
            $query->where('profil_id', $request->profil_id);
        }

        if ($request->filled('niveau_numerique')) {
            $query->where('niveau_numerique', $request->niveau_numerique);
        }

        if ($request->filled('ministere_id')) {
            $query->where('ministere_id', $request->ministere_id);
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                    ->orWhere('prenom', 'like', "%$search%")
                    ->orWhere('matricule', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Données pour les filtres
        $profils = Profil::where('is_active', true)->get();
        $ministeres = Ministere::orderBy('nom')->get();
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
            'regions',
            'niveauxNumeriques',
            'experiences',
            'deploymentOptions'
        ));
    }

    /**
     * Affiche le détail d'un candidat.
     */
    public function show(Request $request, User $user)
    {
        $user->load(['profil', 'ministere', 'regionChoices.region', 'dynamicAnswers']);

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
            'nextId'
        ));
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
            'regionalChoices'
        ));
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
