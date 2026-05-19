# Audit Terrain — Présentation du projet

## But du projet
Audit Terrain est une application de gestion et de déploiement d'équipes terrain pour des missions d'audit. Elle permet :
- l'enregistrement et la gestion des candidats (profil, niveau, affectation),
- la création et la gestion d'équipes (teams),
- l'affectation manuelle ou automatique des candidats aux équipes,
- la collecte des réponses dynamiques et motivations par candidat,
- des outils d'administration (tableau de bord, gestion des questions dynamiques, motivations, etc.).

## Public cible
- Administrateurs (gestion des candidats, équipes, répartition opérationnelle).
- Coordinateurs terrain (création d'équipes, répartition des agents).
- Développeurs / contributeurs souhaitant comprendre et participer au code.

## Stack technique
- Backend: PHP 8.x avec Laravel (routes, controllers, Blade views, Eloquent).
- Frontend: Blade + Bootstrap (modals), Chart.js pour graphiques, JavaScript vanilla pour interactions (drag & drop).
- Base de données: MySQL / MariaDB (migrations présentes dans `database/migrations`).
- Outils: Composer, npm (si vous utilisez les assets), PHPUnit pour les tests.

## Structure importante
- `app/Http/Controllers/` : contrôleurs (ex. `AdminCandidateController`, `AdminOperationsResearchController`).
- `app/Models/` : modèles Eloquent (ex. `User`, `Team`, `Profil`, `UserDynamicAnswer`).
- `resources/views/admin/` : vues administratives (dashboard, candidates, operations-research, etc.).
- `routes/web.php` : routes web protégées par middleware `admin.auth`.
- `database/migrations/` : migrations pour la structure de la BDD.
- `scripts/` : scripts utiles (tests manuels/quick-scripts).

## Points fonctionnels clés
- Gestion des candidats : listing, détail, suppression (via `AdminCandidateController::destroy`).
- Recherche Opérationnelle (`/admin/recherche-operationnelle`) :
  - Drag & drop pour déplacer un agent entre équipes.
  - Répartition automatique (`autoDistribute`) paramétrable par nombre d'équipes.
  - Réinitialisation du déploiement (`resetDeployment`) pour remettre les assignations à zéro.
  - Edition du `profil` d'un agent depuis l'interface opérationnelle.

## Installation locale (rapide)
1. Cloner le dépôt et entrer dans le dossier :

   git clone <repo-url>
   cd audit_terrain

2. Installer les dépendances PHP :

   composer install

3. Copier le fichier d'environnement et configurer la base :

   cp .env.example .env
   # modifier .env : DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL

4. Générer la clé d'application :

   php artisan key:generate

5. Créer la base et exécuter les migrations :

   php artisan migrate
   php artisan db:seed  # si vous avez des seeders

6. (Optionnel) Installer les assets front-end et les compiler :

   npm install
   npm run dev

7. Lancer le serveur local :

   php artisan serve --host=127.0.0.1 --port=8007

## Commandes utiles
- Linter PHP : `php -l path/to/file.php`
- Refresh/views cache : `php artisan view:clear` / `php artisan view:cache`
- Run tests : `vendor/bin/phpunit` ou `php artisan test`

## Conventions et bonnes pratiques
- Utiliser des transactions (`DB::transaction`) pour les opérations multi-étapes qui modifient plusieurs tables.
- Faire la validation côté serveur pour les règles métiers (ex: unicité d'un `profil` dans une équipe).
- Préférer les formulaires classiques (avec CSRF) pour garder la compatibilité et la simplicité.
- Respecter les conventions Laravel (noms de méthodes, ressources, etc.).

## Variables d'environnement importantes
- `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

## Où commencer pour contribuer
1. Lancer l'application localement (voir Installation).
2. Lire les contrôleurs d'administration dans `app/Http/Controllers/` pour comprendre la logique.
3. Pour ajouter une fonctionnalité UI, vérifier `resources/views/admin/` et les routes associées dans `routes/web.php`.
4. Ouvrir une branche par fonctionnalité et fournir des PRs claires avec description et étapes pour tester.

## Ressources / contacts
- Auteur / Mainteneur principal : voir `composer.json` ou `README.md` existant.
- Pour questions techniques : ouvrir une issue dans le dépôt.

---

Fichier généré automatiquement le: 2026-05-19
