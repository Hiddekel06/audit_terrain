<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRegionChoice;
use App\Models\Region;
use App\Models\Motivation;
use App\Models\DynamicQuestion;
use App\Models\UserDynamicAnswer;
use App\Models\UserRegionChoiceMotivation;
use App\Models\Profil;
use App\Models\Ministere;

class AdminDashboardController extends Controller
{
    /**
     * Affiche le dashboard admin avec les statistiques principales.
     */
    public function index()
    {
        $totalUsers = User::count();
        $completedUsers = UserRegionChoice::distinct('user_id')->count('user_id');
        $totalDynamicAnswers = UserDynamicAnswer::count();
        $dynamicRespondents = UserDynamicAnswer::distinct('user_id')->count('user_id');
        $dynamicRespondentsRate = $completedUsers > 0
            ? (int) round(($dynamicRespondents / $completedUsers) * 100)
            : 0;

        // Distribution par profil
        $candidatesByProfil = User::selectRaw('profil_id, profil.libelle, COUNT(*) as total')
            ->leftJoin('profil', 'users.profil_id', '=', 'profil.id')
            ->groupBy('profil_id', 'profil.libelle')
            ->orderByDesc('total')
            ->get();

        // Distribution par niveau numérique
        $candidatesByNiveau = User::selectRaw('niveau_numerique, COUNT(*) as total')
            ->whereNotNull('niveau_numerique')
            ->groupBy('niveau_numerique')
            ->get()
            ->mapWithKeys(fn($item) => [$item->niveau_numerique => $item->total]);

        // Distribution par disponibilité
        $candidatesByDisponibilite = User::selectRaw('disponibilite, COUNT(*) as total')
            ->whereNotNull('disponibilite')
            ->groupBy('disponibilite')
            ->get()
            ->mapWithKeys(fn($item) => [$item->disponibilite => $item->total]);

        // Top ministères
        $candidatesByMinistere = User::selectRaw('ministere_id, ministeres.nom, COUNT(*) as total')
            ->leftJoin('ministeres', 'users.ministere_id', '=', 'ministeres.id')
            ->groupBy('ministere_id', 'ministeres.nom')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Tendances globales (tous choix confondus)
        $tendances = UserRegionChoice::selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        
        // Tendances par choix 1, 2, 3
        $tendancesChoix1 = UserRegionChoice::where('ordre', 1)
            ->selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        $tendancesChoix2 = UserRegionChoice::where('ordre', 2)
            ->selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        $tendancesChoix3 = UserRegionChoice::where('ordre', 3)
            ->selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        
        $motivationStats = \App\Models\UserRegionChoice::statsMotivations();
        $motivationsLibres = UserRegionChoiceMotivation::whereNotNull('motivation_libre')->orderByDesc('id')->get();
        $motivations = Motivation::withTrashed()->orderBy('libelle')->get();
        $dynamicQuestions = DynamicQuestion::with('options')
            ->withCount('answers')
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $profils = Profil::where('is_active', true)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'completedUsers',
            'totalDynamicAnswers',
            'dynamicRespondents',
            'dynamicRespondentsRate',
            'candidatesByProfil',
            'candidatesByNiveau',
            'candidatesByDisponibilite',
            'candidatesByMinistere',
            'tendances',
            'tendancesChoix1',
            'tendancesChoix2',
            'tendancesChoix3',
            'motivationStats',
            'motivationsLibres',
            'motivations',
            'dynamicQuestions',
            'profils'
        ));
    }
}
