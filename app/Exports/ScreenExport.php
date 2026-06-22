<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ScreenExport implements FromCollection
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];

        // Agents libres
        foreach ($this->data['freeAgents'] as $a) {
            $rows[] = [
                'TYPE' => 'LIBRE',
                'EQUIPE' => '-',
                'NOM' => $a['name'],
                'PROFIL' => $a['profil'] ?? '',
            ];
        }

        // Équipes
        foreach ($this->data['teams'] as $t) {
            foreach ($t['members'] as $m) {
                $rows[] = [
                    'TYPE' => 'EQUIPE',
                    'EQUIPE' => $t['name'],
                    'NOM' => $m['name'],
                    'PROFIL' => '',
                ];
            }
        }

        return collect($rows);
    }
}