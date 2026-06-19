<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuizResultsExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $results, private $selectedQuestion = null)
    {
    }

    public function collection(): Collection
    {
        return $this->results->map(function ($result) {
            $totalPossible = $result->quiz->questions->sum('points');
            $percentage = $totalPossible > 0 ? round(($result->score / $totalPossible) * 100, 2) : 0;
            
            $row = [
                'nom' => $result->user->nom,
                'prenom' => $result->user->prenom,
                'matricule' => $result->user->matricule,
                'profil' => $result->user->profil?->libelle ?? 'Non défini',
                'structure' => $result->user->ministere?->nom ?? 'Non renseigné',
                'quiz' => $result->quiz->titre,
                'score' => $result->score,
                'total_possible' => $totalPossible,
                'percentage' => $percentage . '%',
                'date_passage' => $result->created_at->format('d/m/Y H:i'),
            ];

            if ($this->selectedQuestion) {
                if ($result->quiz_id !== $this->selectedQuestion->quiz_id) {
                    $row['reponse_selected'] = 'N/A (Quiz différent)';
                } else {
                    $userAns = $result->answers_json[$this->selectedQuestion->id] ?? null;
                    $userAnsIds = is_array($userAns) ? array_map('intval', $userAns) : ($userAns !== null ? [intval($userAns)] : []);
                    
                    if (empty($userAnsIds)) {
                        $row['reponse_selected'] = 'Non répondu';
                    } else {
                        $selectedOptions = [];
                        foreach ($this->selectedQuestion->options as $option) {
                            if (in_array($option->id, $userAnsIds)) {
                                $selectedOptions[] = $option->libelle . ($option->is_correct ? ' (Correct)' : ' (Incorrect)');
                            }
                        }
                        $row['reponse_selected'] = implode(', ', $selectedOptions);
                    }
                }
            }

            return $row;
        });
    }

    public function headings(): array
    {
        $headers = [
            'Nom',
            'Prénom',
            'Matricule',
            'Profil',
            'Structure',
            'Quiz',
            'Score obtenu',
            'Score max',
            'Pourcentage',
            'Date de passage',
        ];

        if ($this->selectedQuestion) {
            $headers[] = 'Réponse : ' . $this->selectedQuestion->libelle;
        }

        return $headers;
    }
}
