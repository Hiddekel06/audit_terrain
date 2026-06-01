# Audit Terrain — Vue d'ensemble du projet

## Résumé
Audit Terrain est une application Laravel dédiée à la gestion d'un vivier de candidats, au pilotage administratif des structures ministérielles et au déploiement d'équipes terrain pour des missions d'audit. Le projet couvre tout le cycle métier: inscription, enrichissement des profils, affectation aux équipes, répartition manuelle ou automatique, et administration des données opérationnelles.

L'objectif principal est de permettre à une équipe de coordination de constituer des équipes cohérentes à partir de données réelles, tout en gardant une interface simple, rapide et suffisamment souple pour s'adapter à des contextes de terrain variables.

## Ce Que Fait L'Application
- Inscription et suivi des candidats.
- Gestion du profil principal, du profil initial et de la direction.
- Collecte des choix régionaux, motivations et réponses dynamiques.
- Gestion des équipes terrain.
- Affectation manuelle par glisser-déposer.
- Prévisualisation de déploiement avant application.
- Optimisation automatique du déploiement à partir des données disponibles.
- Import Excel des candidats avec prévisualisation, normalisation et gestion des doublons.
- Import Excel des candidats avec deux modes distincts: création classique et mise à jour des téléphones par matricule.
- Génération de rapports CSV pour les lignes ignorées lors des imports.
- Visualisation de la répartition des agents par ministère et par profil.
- Administration des questions dynamiques, motivations et vues de synthèse.

## Public Cible
- Administrateurs qui gèrent les candidats, les équipes et les déploiements.
- Coordinateurs terrain qui préparent les affectations.
- Contributeurs techniques qui doivent comprendre rapidement le flux métier.

## Stack Technique
- Backend: PHP 8.4 avec Laravel 13.
- Frontend: Blade, Bootstrap, JavaScript vanilla.
- Visualisation: Chart.js sur certaines pages de synthèse.
- Base de données: MySQL / MariaDB via migrations Laravel.
- Outils: Composer, npm si nécessaire, PHPUnit / `php artisan test`.

## Architecture Fonctionnelle

### 1. Cycle Candidat
Le module candidat gère:
- la création du compte candidat,
- la conservation du profil initial,
- la saisie d'une direction libre,
- les réponses aux questions dynamiques,
- les motivations et choix régionaux,
- l'édition et la suppression en administration.

Points importants:
- le champ `profil_initial_id` permet de conserver l'état d'origine du candidat,
- le champ `direction` est maintenant éditable et persistant,
- l'affichage admin distingue le profil initial du profil courant.

### 2. Recherche Opérationnelle
La page `/admin/recherche-operationnelle` sert de poste de pilotage pour les équipes.

Elle permet:
- la création manuelle d'équipes,
- l'affectation manuelle d'un candidat vers une équipe,
- le déplacement par drag & drop,
- la libération d'un membre vers les candidats libres,
- la modification du profil d'un agent dans une modale dédiée,
- la réinitialisation complète du déploiement,
- la simulation de scénarios avant application.

Le drag & drop d'aperçu permet de déplacer des membres d'une équipe à une autre avec confirmation.

### 3. Déploiement Paramétrable
Le déploiement a été rendu plus souple pour couvrir plusieurs cas:
- blocs mixtes de répartition, par exemple `4 équipes de 3` puis `2 équipes de 4`,
- prévisualisation avant écriture en base,
- mode d'optimisation automatique qui exploite le stock disponible sans paramètre saisi,
- bouton de déploiement réel volontairement désactivé côté front tant que le flux final n'est pas validé.

Les blocs de répartition peuvent aussi intégrer des quotas par profil par équipe.

### 4. Répartition Par Ministère
La page `/admin/ministeres` remplace le lien d'indicateurs de la sidebar et affiche:
- le volume total d'agents par ministère,
- le détail par profil (chefs d'équipe, auditeurs, supports),
- une carte compacte et lisible inspirée de la vue des régions.

Le rendu est actuellement basé sur des icônes Bootstrap côté interface, sans dépendance à des logos ministériels.

L'idée métier est de ne plus penser uniquement en “nombre d'équipes fixe”, mais en “plan de déploiement” adaptable à la réalité du terrain.

### 5. Import Excel Et Rapports
Le module d'import des candidats repose sur une prévisualisation avant confirmation.

Il permet notamment:
- de normaliser les données importées,
- de reconnaître les ministères à partir d'alias et de variantes,
- de détecter les doublons de matricule ou de téléphone,
- d'importer soit de nouveaux agents, soit de mettre à jour uniquement les téléphones à partir du matricule,
- de conserver un historique des lignes ignorées dans un fichier CSV.

Les modèles téléchargeables sont des fichiers Excel `.xlsx`:
- modèle classique pour la création d'agents,
- modèle téléphone pour la mise à jour des numéros.

Les rapports d'import sont consultables dans `/admin/import-reports`.
La sidebar admin affiche désormais ce lien à la place de l'ancien accès aux motivations.

## Flux De Déploiement
### Mode 1 - Prévisualiser Des Blocs
L'utilisateur définit un ou plusieurs blocs:
- nombre d'équipes,
- taille des équipes.

Le système calcule ensuite un aperçu sans écrire en base.

### Mode 2 - Optimisation Automatique
L'utilisateur ne saisit aucun paramètre métier fin.
Le script observe les candidats libres et construit le meilleur plan possible en fonction des profils disponibles.

Ce mode est utile pour:
- les déploiements nationaux,
- les phases où les régions ne sont pas encore intégrées au calcul,
- les scénarios où l'on veut simplement le meilleur rendu possible avec les données présentes.

### Mode 3 - Application Réelle
Le déploiement réel est conservé dans le code, mais le bouton front est grisé pour l'instant.
Ce mode créera les équipes et affectera réellement les candidats.

## Règles Métier Importantes
- Une équipe ne doit pas recevoir deux candidats avec le même profil quand cette règle est appliquée.
- Les profils peuvent être ajustés depuis l'interface opérationnelle.
- Les régions sont filtrables côté interface, mais l'optimisation automatique fonctionne aujourd'hui surtout sur le stock national.
- Les opérations qui touchent plusieurs tables passent par des transactions.
- La page ministères agrège les agents par `ministere_id` et calcule les compteurs par profil.
- Les imports refusent les faux matricules numériques et vérifient aussi les doublons de téléphone.
- Une migration de nettoyage a été ajoutée pour déplacer les anciens matricules qui contenaient un numéro de téléphone vers le champ `telephone`, puis laisser `matricule` vide pour ces cas.

## Fichiers Clés
- [routes/web.php](../routes/web.php) : routes principales du domaine candidat, admin et recherche opérationnelle.
- [app/Http/Controllers/AdminCandidateController.php](../app/Http/Controllers/AdminCandidateController.php) : listing, détail et suppression des candidats.
- [app/Http/Controllers/AdminOperationsResearchController.php](../app/Http/Controllers/AdminOperationsResearchController.php) : création d'équipes, simulation, optimisation et affectation.
- [app/Http/Controllers/AdminMinistereStatsController.php](../app/Http/Controllers/AdminMinistereStatsController.php) : synthèse des agents par ministère et par profil.
- [resources/views/admin/candidates/index.blade.php](../resources/views/admin/candidates/index.blade.php) : liste des candidats et modale d'édition rapide.
- [resources/views/admin/operations-research.blade.php](../resources/views/admin/operations-research.blade.php) : poste de pilotage du déploiement.
- [resources/views/admin/ministeres.blade.php](../resources/views/admin/ministeres.blade.php) : tableau de bord ministériel compact.
- [app/Http/Controllers/AdminImportReportsController.php](../app/Http/Controllers/AdminImportReportsController.php) : liste et téléchargement des rapports d'import.
- [resources/views/admin/import_reports/index.blade.php](../resources/views/admin/import_reports/index.blade.php) : vue des rapports CSV générés par les imports.
- [resources/views/partials/admin-sidebar.blade.php](../resources/views/partials/admin-sidebar.blade.php) : navigation admin avec le lien vers les rapports d'import.
- [resources/views/utilisateur_form.blade.php](../resources/views/utilisateur_form.blade.php) : formulaire utilisateur avec direction et profil.
- [database/migrations/](../database/migrations/) : historique des évolutions structurelles.

## Évolutions Récentes Notables
- **Refonte Design "Clean Enterprise"** : Migration du module candidats vers le thème Émeraude (Glass-cards, bordures fines).
- **Intégration Chauffeur & Superviseur** : Ces profils sont maintenant des contraintes majeures du script de répartition automatique.
- **Système de Permutation (Swap)** : Possibilité d'échanger deux agents par drag & drop avec confirmation dynamique.
- **Cartes d'Équipes Minimalistes** : Passage à un design en liste avec points de statut pour une meilleure lisibilité.
- **Sécurisation des Profils Admin** : Chauffeurs et Superviseurs exclus du formulaire public et protégés côté serveur.
- Suppression d'un candidat depuis l'administration.
- Edition du profil et de la direction depuis la vue candidat et la vue opérationnelle.
- Conservation du profil initial.
- Ajout du champ `direction` dans l'inscription et dans l'administration.
- Drag & drop pour affecter les membres aux équipes.
- Prévisualisation de déploiement et réinitialisation.
- Répartition paramétrable par blocs.
- Mode d'optimisation automatique basé sur les candidats disponibles.
- Nouvelle vue ministères avec synthèse par profil.
- Nouveau module de rapports d'import pour analyser les lignes ignorées.
- Séparation des imports admin entre création classique et mise à jour des téléphones.
- Passage des modèles d'import en Excel `.xlsx`.
- Migration de correction des anciens matricules qui étaient en réalité des numéros de téléphone.
- Sidebar mise à jour pour pointer vers la répartition ministérielle.

## Installation Locale
1. Cloner le dépôt et entrer dans le dossier.
2. Installer les dépendances PHP avec `composer install`.
3. Copier `.env.example` vers `.env` et configurer la base.
4. Générer la clé d'application avec `php artisan key:generate`.
5. Lancer les migrations avec `php artisan migrate`.
6. Démarrer le serveur local avec `php artisan serve --host=127.0.0.1 --port=8007`.

## Commandes Utiles
- Vérifier un fichier PHP: `php -l path/to/file.php`
- Vider / reconstruire le cache des vues: `php artisan view:clear` puis `php artisan view:cache`
- Lancer les tests: `php artisan test`
- Lancer les migrations: `php artisan migrate`

## Bonnes Pratiques Du Projet
- Utiliser `DB::transaction()` pour les opérations qui touchent plusieurs tables.
- Valider les données côté serveur avant toute affectation.
- Garder les formulaires simples et fiables avec CSRF.
- Préférer des changements incrémentaux et testables pour les flux de déploiement.

## Variables D'Environnement Utiles
- `APP_ENV`
- `APP_DEBUG`
- `APP_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## Pour Commencer À Contribuer
1. Lire [app/Http/Controllers/AdminOperationsResearchController.php](../app/Http/Controllers/AdminOperationsResearchController.php) pour comprendre le moteur de déploiement.
2. Lire [resources/views/admin/operations-research.blade.php](../resources/views/admin/operations-research.blade.php) pour voir l'interface de pilotage.
3. Lire [app/Http/Controllers/AdminMinistereStatsController.php](../app/Http/Controllers/AdminMinistereStatsController.php) et [resources/views/admin/ministeres.blade.php](../resources/views/admin/ministeres.blade.php) pour la page ministères.
4. Vérifier [routes/web.php](../routes/web.php) pour relier les actions aux écrans.
5. Se baser sur les migrations récentes pour comprendre l'évolution du modèle de données.

## Note De Contexte
Ce document décrit l'état fonctionnel actuel du projet et sert de base de compréhension pour les nouveaux contributeurs comme pour les mainteneurs.

---

Dernière mise à jour: 2026-05-26
