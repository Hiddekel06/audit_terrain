<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinistereSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ministeres = [
            ['id' => 31, 'nom' => 'Ministère de la Justice, Garde des Sceaux', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 32, 'nom' => 'Ministère de l’Énergie, du Pétrole et des Mines', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 33, 'nom' => 'Ministère de l’Intégration Africaine, des Affaires étrangères et des Sénégalais de l’Extérieur', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 34, 'nom' => 'Ministère des Forces Armées', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 35, 'nom' => 'Ministère de l’Intérieur et de la Sécurité publique', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 36, 'nom' => 'Ministère de l’Économie, du Plan et de la Coopération', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 37, 'nom' => 'Ministère des Finances et du Budget', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 38, 'nom' => 'Ministère de l’Enseignement supérieur, de la Recherche et de l’Innovation', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 39, 'nom' => 'Ministère des Transports Terrestres et Aériens', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 40, 'nom' => 'Ministère de la Communication, des Télécommunications et du Numérique', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 41, 'nom' => 'Ministère de l’Agriculture, de la Souveraineté Alimentaire et de l’Elevage', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 42, 'nom' => 'Ministère de l’Hydraulique et de l’Assainissement', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 43, 'nom' => 'Ministère de la Santé et de l’Hygiène Publique', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 44, 'nom' => 'Ministère de la Famille, de l’Action sociale et des Solidarités', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 45, 'nom' => 'Ministère de l’Emploi et de la Formation Professionnelle et Technique', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 46, 'nom' => 'Ministère de l’Environnement et de la Transition Ecologique', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 47, 'nom' => 'Ministère de l’Urbanisme, des Collectivités Territoriales et de l’Aménagement des Territoires', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 48, 'nom' => 'Ministère de l’Industrie et du Commerce', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 49, 'nom' => 'Ministère des Pêches et de l’Economie Maritime', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 50, 'nom' => 'Ministère de la Jeunesse et des Sports', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 51, 'nom' => 'Ministère de la Microfinance et de l’Economie Sociale et Solidaire', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 52, 'nom' => 'Ministère des Infrastructures', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],

            ['id' => 53, 'nom' => 'Ministère de la Culture, de l’Artisanat et du Tourisme', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 54, 'nom' => 'Ministère de la fonction publique , du travail et de la Reforme du service public', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 55, 'nom' => 'Présidence', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 56, 'nom' => 'ANSD', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 57, 'nom' => 'Synapsis', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
            ['id' => 58, 'nom' => 'Senum', 'code' => null, 'parent_id' => null, 'region_id' => 1, 'departement_id' => 47, 'created_at' => '2025-12-09 08:48:04', 'updated_at' => '2025-12-09 08:48:04'],
        ];

        foreach ($ministeres as $ministere) {
            DB::table('ministeres')->updateOrInsert(
                ['id' => $ministere['id']],
                $ministere
            );
        }
    }
}
