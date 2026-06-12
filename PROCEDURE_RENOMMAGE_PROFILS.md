# Procédure de Renommage des Profils Métier

Ce document détaille la marche à suivre pour modifier les libellés des profils "Auditeur" au sein de l'application Audit Terrain. L'objectif est d'aligner la terminologie de la plateforme sur la réalité opérationnelle sans casser la logique technique existante.

## 1. Objectif du Changement

Nous procédons à un basculement des noms affichés (Libellés) selon la correspondance suivante :

| ID | Ancien Libellé | Nouveau Libellé | Rôle Opérationnel |
| :-- | :--- | :--- | :--- |
| **2** | Auditeur | **Auditeur IT** | Profil technique dominant |
| **3** | Auditeur – Support Technique (IT) | **Auditeur Administratif** | Profil administratif / support |

---

## 2. Stratégie Technique : "Affichage vs Logique"

Pour garantir une **stabilité totale** et éviter les erreurs de base de données, nous adoptons la stratégie suivante :

*   **Modification des Libellés (UI)** : Tous les textes visibles par les utilisateurs (Admin et Candidats) sont mis à jour.
*   **Conservation des Codes (Backend)** : Les identifiants techniques (`auditeur` et `auditeur_it`) restent inchangés dans le code PHP et la base de données.
    *   *Pourquoi ?* Cela évite de devoir réécrire les requêtes SQL, les jointures et les calculs de quotas du moteur de déploiement.
*   **Descriptions** : Les descriptions métier restent inchangées (gestion manuelle ultérieure).

---

## 3. Liste des Fichiers à Modifier

### A. La Source (Base de Données)
*   **`database/seeders/ProfilSeeder.php`** : Mise à jour des libellés pour les IDs 2 et 3.
*   **Action requise** : Exécuter `php artisan db:seed --class=ProfilSeeder` après modification.

### B. L'Interface Administrateur (Back-Office)
*   **`app/Http/Controllers/AdminOperationsResearchController.php`** : Mise à jour des étiquettes dans la méthode `deploymentProfiles()` pour les résumés de quotas.
*   **`app/Http/Controllers/AdminMinistereStatsController.php`** : Mise à jour des noms dans les tableaux de statistiques.

### C. L'Interface Candidat (Front-Office)
*   **`resources/views/welcome_profiles.blade.php`** : Mise à jour des titres sur les cartes de présentation des profils (Sections Auditeur et Support).

### D. La Logique Visuelle (Thèmes)
*   **`app/Http/Controllers/AdminCandidateController.php`** : Vérifier que les tests de couleur (ex: `str_contains($label, 'auditeur')`) englobent bien les deux nouveaux noms pour conserver le thème émeraude.

---

## 4. Erreurs à Éviter (Points de Vigilance)

1.  **L'Inversion de sens** : Ne pas confondre l'ID 2 et l'ID 3. L'Auditeur (simple) devient l'IT. Le Support (actuel IT) devient l'Administratif.
2.  **Oubli des Exports** : Si des rapports Excel sont générés, vérifier que les en-têtes de colonnes utilisent bien les nouveaux libellés.
3.  **Casse du moteur de recherche** : S'assurer que les filtres de recherche par profil continuent de fonctionner (ils utilisent normalement les IDs, donc le risque est faible).
4.  **Tests de comparaison en dur** : Éviter dans le futur d'écrire du code comme `if ($user->profil->libelle == "Auditeur")`. Toujours privilégier les IDs ou les codes (`$user->profil->code`).

---

## 5. Validation après changement

Après application, vérifier les points suivants :
- [ ] Le formulaire d'inscription affiche "Auditeur IT" et "Auditeur Administratif".
- [ ] La liste des candidats en admin affiche les nouveaux noms dans la colonne Profil.
- [ ] Le module de déploiement affiche les bons intitulés dans les blocs de quotas.
- [ ] Les couleurs (Thème vert) sont toujours présentes sur les fiches des deux profils.

---
*Date de création : 12 juin 2026*
*Statut : En attente d'exécution*
