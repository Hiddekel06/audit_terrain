<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'identite' => [
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'matricule' => $this->matricule,
                'photo_url' => $this->photo ? url('storage/photos/' . $this->photo) : null,
            ],
            'contact' => [
                'telephone' => $this->telephone,
                'email' => $this->email,
            ],
            'qualification' => [
                'profil' => $this->profil?->libelle,
                'niveau' => $this->niveau_numerique,
                'experiences' => $this->experiences,
            ],
            'structure' => [
                'ministere' => $this->ministere?->nom,
                'direction' => $this->direction,
            ],
            'statut' => $this->validation_status,
            'date_actualisation' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
