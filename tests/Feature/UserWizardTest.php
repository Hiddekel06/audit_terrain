<?php

namespace Tests\Feature;

use App\Models\Profil;
use App\Models\Ministere;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_formulaire_affiche_le_matricule_et_les_profils(): void
    {
        $this->seedProfiles();
        $this->seedMinisteres();

        $response = $this->get(route('utilisateur.form', ['profil_id' => 1]));

        $response->assertOk();
        $response->assertDontSeeText('Profil souhaité');
        $response->assertSeeText('Profil choisi');
        $response->assertSeeText("Chef d'équipe");
        $response->assertSeeText('Matricule');
        $response->assertSeeText('Pas de matricule');
        $response->assertSeeText('Ministère');
        $response->assertSeeText('1. Informations générales');
        $response->assertSeeText('2. Compétences');
        $response->assertSeeText('Section 3 : Niveau numérique');
        $response->assertSeeText('Section 4 : Expérience');
        $response->assertSeeText('Section 5 : Compétences techniques');
    }

    public function test_le_matricule_doit_avoir_le_format_attendu(): void
    {
        $this->seedProfiles();
        $this->seedMinisteres();

        $response = $this->post(route('utilisateur.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Awa',
            'telephone' => '771234567',
            'email' => 'awa@example.com',
            'ministere_id' => 1,
            'disponibilite' => 'immediate',
            'matricule' => '12345',
            'profil_id' => 1,
            'niveau_numerique' => 'intermediaire',
            'experiences' => ['audit_recensement'],
            'competences_techniques' => ['tablette_smartphone'],
        ]);

        $response->assertSessionHasErrors(['matricule']);
    }

    public function test_le_cin_est_accepté_si_aucun_matricule_nest_disponible(): void
    {
        $this->seedProfiles();
        $this->seedMinisteres();

        $response = $this->post(route('utilisateur.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Awa',
            'telephone' => '771234567',
            'email' => 'awa@example.com',
            'ministere_id' => 1,
            'disponibilite' => 'immediate',
            'no_matricule' => 1,
            'cin' => '1234567890123',
            'profil_id' => 1,
            'niveau_numerique' => 'intermediaire',
            'experiences' => ['audit_recensement'],
            'competences_techniques' => ['tablette_smartphone'],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('pending_user_payload', function ($payload) {
            return ($payload['matricule'] ?? null) === '1234567890123';
        });
    }

    public function test_le_flux_complet_persiste_lutilisateur_avec_profil(): void
    {
        $this->seedProfiles();
        $this->seedMinisteres();
        $this->seedRegionsTable();

        $this->withSession([
            'pending_user_payload' => [
                'prenom' => 'Awa',
                'nom' => 'Diallo',
                'matricule' => '123456A',
                'telephone' => '771234567',
                'email' => 'awa@example.com',
                'disponibilite' => 'immediate',
                'profil_id' => 1,
                'niveau_numerique' => 'intermediaire',
                'experiences' => ['audit_recensement', 'biometrie'],
                'competences_techniques' => ['tablette_smartphone', 'excel_donnees'],
                'ministere_id' => 1,
            ],
            'pending_dynamic_answers' => [],
        ]);

        $response = $this->post(route('user_region_choice.store'), [
            'choix_1' => 1,
            'choix_2' => 2,
            'choix_3' => 3,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'nom' => 'Diallo',
            'prenom' => 'Awa',
            'matricule' => '123456A',
            'telephone' => '771234567',
            'email' => 'awa@example.com',
            'disponibilite' => 'immediate',
            'profil_id' => 1,
            'niveau_numerique' => 'intermediaire',
            'ministere_id' => 1,
        ]);

        $createdUser = User::where('matricule', '123456A')->firstOrFail();
        $this->assertSame(['audit_recensement', 'biometrie'], $createdUser->experiences);
        $this->assertSame(['tablette_smartphone', 'excel_donnees'], $createdUser->competences_techniques);
    }

    private function seedProfiles(): void
    {
        Profil::insert([
            [
                'id' => 1,
                'libelle' => "Chef d'équipe",
                'code' => 'chef_equipe',
                'description' => 'Coordination globale de l\'équipe d\'audit sur site.',
                'ordre' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'libelle' => 'Auditeur',
                'code' => 'auditeur',
                'description' => 'Réaliser l’audit et garantir la fiabilité des données.',
                'ordre' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'libelle' => 'Auditeur – Support Technique (IT)',
                'code' => 'auditeur_it',
                'description' => 'Garantir la fiabilité technique et la qualité des données.',
                'ordre' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedRegionsTable(): void
    {
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->timestamps();
            });
        }

        DB::table('regions')->insert([
            ['id' => 1, 'nom' => 'Dakar'],
            ['id' => 2, 'nom' => 'Thiès'],
            ['id' => 3, 'nom' => 'Saint-Louis'],
        ]);
    }

    private function seedMinisteres(): void
    {
        Ministere::insert([
            [
                'id' => 1,
                'nom' => 'Ministère de l\'Intérieur',
                'code' => 'MI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nom' => 'Ministère de l\'Économie',
                'code' => 'ME',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nom' => 'Ministère de l\'Éducation',
                'code' => 'MED',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}