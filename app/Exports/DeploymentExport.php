<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeploymentExport implements FromCollection, WithHeadings
{
    protected $teams;

    public function __construct($teams)
    {
        $this->teams = $teams;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->teams as $team) {

            foreach ($team->members as $member) {

                $rows->push([
                    'Equipe'     => $team->nom,
                    'Matricule'  => $member->matricule,
                    'Nom'        => $member->nom,
                    'Prenom'     => $member->prenom,
                    'Telephone'  => $member->telephone,
                    'Profil'     => optional($member->profil)->libelle,
                    'Ministere'  => optional($member->ministere)->nom,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Equipe',
            'Matricule',
            'Nom',
            'Prenom',
            'Telephone',
            'Profil',
            'Ministere',
        ];
    }
}