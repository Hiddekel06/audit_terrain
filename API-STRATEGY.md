# Stratégie d'Interconnexion API — Audit Terrain

## 1. État des lieux
- **Framework** : Laravel 13.
- **Statut Sanctum** : Non installé par défaut (absent de `composer.json` et `config/`).
- **Objectif** : Permettre à une plateforme Laravel tierce de récupérer la liste des agents et leurs profils.

## 2. Architecture Technique Proposée
Pour une mise en place rapide et sécurisée sans surcharge :

### A. Sécurité
L'utilisation de **Laravel Sanctum** est recommandée. 
1. **Installation** : `composer require laravel/sanctum` + `php artisan sanctum:install`.
2. **Authentification** : Utilisation de "Personal Access Tokens". Un token unique sera généré pour la plateforme distante.
3. **Transport** : Le token devra être envoyé dans le header `Authorization: Bearer {TOKEN}`.

### B. Point d'entrée (Endpoint)
- **URL** : `GET /api/v1/agents`
- **Filtres optionnels** : 
    - `?status=officiel_inscrit` : Pour ne récupérer que les agents validés.
    - `?profil=auditeur` : Filtrage par type de profil.

### C. Format de Réponse (JSON)
L'API retournera un objet structuré via une `JsonResource` pour masquer les champs sensibles (mots de passe, tokens internes) :
```json
{
    "data": [
        {
            "id": 187,
            "identite": {
                "nom": "SARR",
                "prenom": "Moussa",
                "matricule": "660805M"
            },
            "contact": {
                "telephone": "771234567",
                "email": "m.sarr@exemple.sn"
            },
            "qualification": {
                "profil": "Auditeur IT",
                "niveau": "Expert",
                "experiences": ["Audit", "Biométrie"]
            },
            "structure": {
                "ministere": "Ministère de la Santé",
                "direction": "DSI"
            },
            "statut": "officiel_inscrit",
            "date_actualisation": "2026-06-16T10:00:00Z"
        }
    ],
    "links": { ... },
    "meta": { "total": 150 }
}
```

## 3. Étapes de mise en œuvre (À venir)
1. Installation de Sanctum.
2. Création du modèle de Token pour "Plateforme Tierce".
3. Création de l'API Controller `AgentApiController`.
4. Création de la ressource de formatage `AgentResource`.
5. Configuration de la route dans `routes/api.php`.
