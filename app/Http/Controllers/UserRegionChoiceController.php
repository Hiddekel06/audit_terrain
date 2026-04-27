<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserRegionChoice;
use App\Models\Region;
use App\Models\Motivation;
use App\Models\UserDynamicAnswer;
use App\Models\UserRegionChoiceMotivation;

class UserRegionChoiceController extends Controller
{
    /**
     * Affiche le formulaire de choix des régions pour l'utilisateur.
     */
    public function create()
    {
        if (!session()->has('pending_user_payload')) {
            return redirect()->route('utilisateur.form')->withErrors([
                'user' => 'Veuillez d\'abord remplir le formulaire d\'identification.',
            ]);
        }

        $regions = Region::all();
        $motivations = Motivation::orderBy('libelle')->get();
        return view('choix_regions', compact('regions', 'motivations'));
    }

    /**
     * Enregistre les choix de régions de l'utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'choix_1' => 'required|different:choix_2,choix_3|exists:regions,id',
            'choix_2' => 'required|different:choix_1,choix_3|exists:regions,id',
            'choix_3' => 'required|different:choix_1,choix_2|exists:regions,id',
        ]);

        $pendingUser = session('pending_user_payload');
        $pendingAnswers = session('pending_dynamic_answers', []);

        if (!$pendingUser) {
            return redirect()->route('utilisateur.form')->withErrors(['user' => 'Utilisateur introuvable. Veuillez vous identifier.']);
        }

        DB::transaction(function () use ($request, $pendingUser, $pendingAnswers) {
            $matricule = $pendingUser['matricule'] ?? null;

            if (empty($matricule)) {
                $matricule = $this->generateUniqueTemporaryMatricule();
            }

            $user = User::create([
                'prenom' => $pendingUser['prenom'],
                'nom' => $pendingUser['nom'],
                'telephone' => $pendingUser['telephone'] ?? null,
                'matricule' => $matricule,
            ]);

            foreach ($pendingAnswers as $answer) {
                UserDynamicAnswer::create([
                    'user_id' => $user->id,
                    'dynamic_question_id' => $answer['dynamic_question_id'],
                    'dynamic_question_option_id' => $answer['dynamic_question_option_id'] ?? null,
                    'answer_text' => $answer['answer_text'] ?? null,
                ]);
            }

            foreach ([1, 2, 3] as $i) {
                $regionChoice = UserRegionChoice::create([
                    'user_id' => $user->id,
                    'region_id' => $request->input('choix_' . $i),
                    'ordre' => $i,
                ]);

                // Motivations types (checkbox)
                $motivationIds = $request->input('motivations_' . $i, []);
                foreach ($motivationIds as $motivationId) {
                    UserRegionChoiceMotivation::create([
                        'user_region_choice_id' => $regionChoice->id,
                        'motivation_id' => $motivationId,
                        'motivation_libre' => null,
                    ]);
                }

                // Motivation personnalisée (champ texte si 'Autre' coché)
                $motivationLibre = $request->input('motivation_autre_' . $i);
                if ($request->has('motivation_autre_' . $i) && $motivationLibre) {
                    UserRegionChoiceMotivation::create([
                        'user_region_choice_id' => $regionChoice->id,
                        'motivation_id' => null,
                        'motivation_libre' => $motivationLibre,
                    ]);
                }
            }
        });

        session()->forget([
            'pending_user_payload',
            'pending_dynamic_answers',
            'user_id',
        ]);

        return view('merci');
    }

    private function generateUniqueTemporaryMatricule(): string
    {
        $fakeMatricule = 'T' . str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT) . chr(mt_rand(65, 90));

        while (User::where('matricule', $fakeMatricule)->exists()) {
            $fakeMatricule = 'T' . str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT) . chr(mt_rand(65, 90));
        }

        return $fakeMatricule;
    }
}
