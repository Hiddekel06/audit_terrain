<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuizResultsExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Collection $results, 
        private $selectedQuestionA = null, 
        private $selectedQuestionB = null
    ) {
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

            if ($this->selectedQuestionA) {
                $row['reponse_a'] = $this->getQuestionAnswerText($result, $this->selectedQuestionA);
            }

            if ($this->selectedQuestionB) {
                $row['reponse_b'] = $this->getQuestionAnswerText($result, $this->selectedQuestionB);
            }

            return $row;
        });
    }

    private function getQuestionAnswerText($result, $question): string
    {
        if ($result->quiz_id !== $question->quiz_id) {
            return 'N/A (Quiz différent)';
        }
        
        $userAns = $result->answers_json[$question->id] ?? null;
        $userAnsIds = is_array($userAns) ? array_map('intval', $userAns) : ($userAns !== null ? [intval($userAns)] : []);
        
        if (empty($userAnsIds)) {
            return 'Non répondu';
        }

        $selectedOptions = [];
        foreach ($question->options as $option) {
            if (in_array($option->id, $userAnsIds)) {
                $selectedOptions[] = $option->libelle . ($option->is_correct ? ' (Correct)' : ' (Incorrect)');
            }
        }
        return implode(', ', $selectedOptions);
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

        if ($this->selectedQuestionA) {
            $headers[] = 'Réponse A : ' . $this->selectedQuestionA->libelle;
        }

        if ($this->selectedQuestionB) {
            $headers[] = 'Réponse B : ' . $this->selectedQuestionB->libelle;
        }

        return $headers;
    }
}
