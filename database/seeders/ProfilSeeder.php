<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilSeeder extends Seeder
{
    public function run(): void
    {
        $profils = [
            [
                'id' => 1,
                'libelle' => "Chef d'équipe",
                'code' => 'chef_equipe',
                'description' => "Coordination globale de l'équipe d'audit sur site.",
                'ordre' => 1,
                'is_active' => true,
                'created_at' => '2026-05-04 00:00:00',
                'updated_at' => '2026-05-04 00:00:00',
            ],
            [
                'id' => 2,
                'libelle' => 'Auditeur',
                'code' => 'auditeur',
                'description' => 'Réaliser l’audit et garantir la fiabilité des données.',
                'ordre' => 2,
                'is_active' => true,
                'created_at' => '2026-05-04 00:00:00',
                'updated_at' => '2026-05-04 00:00:00',
            ],
            [
                'id' => 3,
                'libelle' => 'Auditeur – Support Technique (IT)',
                'code' => 'auditeur_it',
                'description' => 'Garantir la fiabilité technique et la qualité des données.',
                'ordre' => 3,
                'is_active' => true,
                'created_at' => '2026-05-04 00:00:00',
                'updated_at' => '2026-05-04 00:00:00',
            ],
            [
                'id' => 4,
                'libelle' => 'Chauffeur',
                'code' => 'chauffeur',
                'description' => 'Assure le transport et la mobilité des équipes sur le terrain.',
                'ordre' => 4,
                'is_active' => true,
                'created_at' => '2026-05-04 00:00:00',
                'updated_at' => '2026-05-04 00:00:00',
            ],
            [
                'id' => 5,
                'libelle' => 'Superviseur',
                'code' => 'superviseur',
                'description' => 'Supervise et coordonne les activités de l’équipe sur le terrain.',
                'ordre' => 5,
                'is_active' => true,
                'created_at' => '2026-05-04 00:00:00',
                'updated_at' => '2026-05-04 00:00:00',
            ],
        ];

        foreach ($profils as $profil) {
            DB::table('profil')->updateOrInsert(
                ['id' => $profil['id']],
                $profil
            );
        }
    }
}