# Stratégie de Gestion des Permissions - Audit Terrain

## 1. Objectif
Mettre en place un système de contrôle d'accès (RBAC) ultra-simplifié pour distinguer les administrateurs complets des simples observateurs.

## 2. Définition des Rôles
Nous utiliserons deux rôles distincts dans la table `user_admins` :

| Rôle | Description | Droits |
| :--- | :--- | :--- |
| `super_admin` | Administrateur Principal | **Tous les droits** : Lecture, Écriture, Suppression, Simulation IA, Déploiement. |
| `viewer` | Observateur | **Lecture uniquement** : Consultation des listes et détails. Accès **INTERDIT** à la Recherche Opérationnelle. |

## 3. Plan d'Action Technique

### A. Base de Données
- Ajout d'une colonne `role` (string) dans la table `user_admins`.
- Valeur par défaut : `viewer` (pour plus de sécurité).

### B. Sécurité (Middleware)
- Mise à jour du middleware `AdminAuth` pour vérifier les droits d'accès.
- Blocage strict de la route `/admin/recherche-operationnelle/*` si le rôle est `viewer`.
- Redirection avec message d'erreur ("Accès restreint") en cas de tentative non autorisée.

### C. Interface Utilisateur (Sidebar)
- Modification de `admin-sidebar.blade.php`.
- Le lien vers la **Recherche Opérationnelle** sera caché (non rendu) pour les comptes `viewer`.

### D. Protection des Actions (Vues)
- Masquer les boutons "Supprimer", "Modifier", "Importer" et "Nouvelle Équipe" dans toutes les vues si l'utilisateur est un `viewer`.
- Désactiver les fonctionnalités de Drag & Drop pour ce rôle.

## 4. Confirmation demandée
Est-ce que cette approche correspond à votre besoin ? Si oui, je procéderai aux modifications dans cet ordre :
1. Migration SQL.
2. Mise à jour du Modèle et Middleware.
3. Mise à jour de la Sidebar.
4. Protection des actions critiques.
