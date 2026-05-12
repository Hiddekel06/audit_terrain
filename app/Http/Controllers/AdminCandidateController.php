<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profil;
use App\Models\Ministere;
use App\Models\UserRegionChoice;

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

        if ($request->filled('disponibilite')) {
            $query->where('disponibilite', $request->disponibilite);
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

        $candidates = $query->orderBy('created_at', 'desc')->paginate(20);

        // Données pour les filtres
        $profils = Profil::where('is_active', true)->get();
        $ministeres = Ministere::orderBy('nom')->get();
        $niveauxNumeriques = ['debutant', 'intermediaire', 'avance', 'expert'];
        $disponibilites = ['immediate', 'sous_7_jours', 'sous_15_jours', 'selon_calendrier'];

        return view('admin.candidates.index', compact(
            'candidates',
            'profils',
            'ministeres',
            'niveauxNumeriques',
            'disponibilites'
        ));
    }

    /**
     * Affiche le détail d'un candidat.
     */
    public function show(User $user)
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

        return view('admin.candidates.show', compact(
            'user',
            'experiences',
            'competencesTechniques',
            'regionalChoices'
        ));
    }

    /**
     * Retourne les stats dashboard.
     */
    public function stats()
    {
        // Métriques globales
        $totalCandidates = User::count();
        $candidatesByProfil = User::selectRaw('profil_id, profil.libelle, COUNT(*) as total')
            ->leftJoin('profil', 'users.profil_id', '=', 'profil.id')
            ->groupBy('profil_id', 'profil.libelle')
            ->orderByDesc('total')
            ->get();

        $candidatesByNiveau = User::selectRaw('niveau_numerique, COUNT(*) as total')
            ->whereNotNull('niveau_numerique')
            ->groupBy('niveau_numerique')
            ->get()
            ->mapWithKeys(fn($item) => [$item->niveau_numerique => $item->total]);

        $candidatesByDisponibilite = User::selectRaw('disponibilite, COUNT(*) as total')
            ->whereNotNull('disponibilite')
            ->groupBy('disponibilite')
            ->get()
            ->mapWithKeys(fn($item) => [$item->disponibilite => $item->total]);

        $candidatesByMinistere = User::selectRaw('ministere_id, ministeres.nom, COUNT(*) as total')
            ->leftJoin('ministeres', 'users.ministere_id', '=', 'ministeres.id')
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
        $totalByProfil = User::where('profil_id', $profil->id)->count();

        $niveauStats = User::where('profil_id', $profil->id)
            ->selectRaw('niveau_numerique, COUNT(*) as total')
            ->groupBy('niveau_numerique')
            ->get()
            ->mapWithKeys(fn($item) => [$item->niveau_numerique => $item->total]);

        // Compétences techniques les plus courantes
        $competencesStats = User::where('profil_id', $profil->id)
            ->get()
            ->flatMap(function ($user) {
                return $user->competences_techniques ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        // Expériences les plus courantes
        $experiencesStats = User::where('profil_id', $profil->id)
            ->get()
            ->flatMap(function ($user) {
                return $user->experiences ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        // Choix régionaux pour ce profil
        $regionalChoices = UserRegionChoice::selectRaw('region_id, region.nom, ordre, COUNT(*) as total')
            ->leftJoin('regions', 'user_region_choices.region_id', '=', 'regions.id')
            ->whereIn('user_id', User::where('profil_id', $profil->id)->pluck('id'))
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
}
