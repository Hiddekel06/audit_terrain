<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CandidatesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $candidates)
    {
    }

    public function collection(): Collection
    {
        return $this->candidates->map(function ($candidate) {
            return [
                'nom' => $candidate->nom,
                'prenom' => $candidate->prenom,
                'matricule' => $candidate->matricule,
                'email' => $candidate->email,
                'telephone' => $candidate->telephone,
                'profil' => $candidate->profil?->libelle,
                'structure' => $candidate->ministere?->nom,
                'direction' => $candidate->direction,
                'niveau_numerique' => $candidate->niveau_numerique,
                'source_type' => $candidate->source_type,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
            'Matricule',
            'Email',
            'Téléphone',
            'Profil',
            'Structure',
            'Direction',
            'Niveau numérique',
            'Source',
        ];
    }
}