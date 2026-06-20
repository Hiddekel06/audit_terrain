<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'matricule',
        'telephone',
        'email',
        'disponibilite',
        'ready_to_deploy_all_regions',
        'profil_id',
        'profil_initial_id',
        'profil_secondaires',
        'niveau_numerique',
        'experiences',
        'competences_techniques',
        'ministere_id',
        'direction',
        'metier',
        'hierarchie',
        'source_type',
        'team_id',
        'validation_status',
        'validation_source',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'experiences' => 'array',
            'competences_techniques' => 'array',
            'profil_secondaires' => 'array',
            'ready_to_deploy_all_regions' => 'boolean',
        ];
    }

    /**
     * Un utilisateur a plusieurs choix de région.
     */
    public function regionChoices()
    {
        return $this->hasMany(UserRegionChoice::class);
    }

    /**
     * Profil initial/choisi par l'utilisateur lors de son inscription.
     */
    public function initialProfil()
    {
        return $this->belongsTo(Profil::class, 'profil_initial_id');
    }

    /**
     * Réponses dynamiques liées au formulaire utilisateur.
     */
    public function dynamicAnswers()
    {
        return $this->hasMany(UserDynamicAnswer::class);
    }

    /**
     * Ministère d'appartenance de l'utilisateur.
     */
    public function ministere()
    {
        return $this->belongsTo(Ministere::class);
    }

    /**
     * Profil souhaité par l'utilisateur.
     */
    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }

    /**
     * Équipe à laquelle appartient l'utilisateur.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
