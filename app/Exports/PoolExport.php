<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PoolExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $agents)
    {
    }

    public function collection(): Collection
    {
        return $this->agents->map(function (array $agent) {
            $name = trim((string) ($agent['name'] ?? ''));

            return [
                'ID' => $agent['id'] ?? '',
                'Matricule' => $agent['matricule'] ?? '',
                'Nom complet' => $name,
                'Telephone' => $agent['telephone'] ?? '',
                'Profil' => $agent['profil'] ?? '',
                'Structure' => $agent['structure'] ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Matricule',
            'Nom complet',
            'Telephone',
            'Profil',
            'Structure',
        ];
    }
}
