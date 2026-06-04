<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SimulationExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected array $teams;

    public function __construct(array $teams)
    {
        $this->teams = $teams;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->teams as $team) {
            $teamName = $team['nom'] ?? 'Équipe';
            
            foreach ($team['members'] as $member) {
                $data[] = [
                    'team' => $teamName,
                    'role' => $member['role'] ?? $member['profil'] ?? '',
                    'name' => $member['name'] ?? '',
                    'profil' => $member['profil'] ?? '',
                ];
            }

            // Ajouter une ligne vide entre les équipes pour la lisibilité
            $data[] = ['', '', '', ''];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Équipe',
            'Rôle (Affectation)',
            'Nom & Prénom',
            'Profil Métier',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Simulation de Déploiement';
    }
}
