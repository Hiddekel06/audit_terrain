# Mémoire du Projet - Audit Terrain

## 02/06/2026 : Intelligence Opérationnelle & Aide à la Décision

### 1. Moteur OR & Aide à la Décision
- **Analyse de Capacité en Temps Réel** : Implémentation d'un calculateur de "Potentiel" global. Le système déduit automatiquement les agents consommés bloc par bloc pour afficher la capacité réelle restante du vivier.
- **Modèle "Quota-Driven"** : Abandon de la saisie manuelle de la taille d'équipe au profit d'un calcul automatique basé sur les quotas par profil.
- **Permutations Avancées** : Optimisation du Drag & Drop pour permettre le remplacement direct d'un agent en poste par un agent libre (permutation atomique équipe <-> vivier).

### 2. Expérience Utilisateur (UX) & Échanges
- **Édition de Profil In-Situ** : Ajout d'un bouton d'édition rapide sur chaque agent. Permet de modifier le profil, la structure ou la direction sans quitter le module de déploiement (avec rechargement automatique).
- **Export Excel Enrichi** : Ajout des colonnes administratives critiques dans le plan de déploiement : **Matricule**, **Téléphone** et **Structure (Ministère)**.

### 3. Fiabilisation Technique
- **Correction de Schéma** : Correction des références à la table `ministeres` (utilisation de `nom` au lieu de `libelle`).
- **Sanitisation Blade** : Résolution des erreurs de "push stack" via une réécriture complète et propre de la vue principale.
- **Unification des logiques** : Fusion des moteurs de simulation et d'optimisation pour une cohérence totale des données.

### Perspectives & Evolutions
- **Contraintes Ministérielles** : Option pour interdire le mélange de différents ministères au sein d'une même unité opérationnelle.
- **Algorithmes de Priorisation** : Intégrer l'ancienneté ou le mérite dans le choix automatique des agents.
- **Optimisation Géographique** : Mise en correspondance stricte entre les vœux de régions des agents et le déploiement.

## 01/06/2026 : Refonte Design & Optimisation Opérationnelle


### 1. Identité Visuelle "Clean Enterprise"
- **Thème Émeraude** : Application de la nouvelle charte graphique sur le module Candidats (Index, Détails, Création, Analyse par profil).
- **Layout & Sidebar** : Mise à jour avec les nouveaux dégradés profonds (`#065f46` vers `#064e3b`) et typographie épurée.
- **Cartes Premium** : Introduction des `glass-card` avec bordures ultra-fines et ombres portées douces.

### 2. Module Recherche Opérationnelle
- **Nouveaux Profils** : Intégration complète des profils `Chauffeur` (clé de voûte) et `Superviseur` dans l'algorithme de répartition automatique.
- **Taille d'équipe** : Passage à une taille par défaut de 5 membres pour inclure systématiquement : 1 Superviseur, 1 Chef, 1 Auditeur, 1 Support, 1 Chauffeur.
- **Mécanisme de Permutation (Swap)** : 
    - Développement d'un système de Drag & Drop permettant d'échanger deux agents en les faisant glisser l'un sur l'autre.
    - Création d'une route backend dédiée (`/swap`) pour garantir l'atomicité de l'échange en base de données.
    - Modale de confirmation dynamique : *"Voulez-vous permuter l'agent X avec l'agent B ?"*.
- **Design Épuré Bleu** : Refonte des cartes d'équipes en format liste (minimaliste) avec indicateurs de statut par points de couleur (Vert/Gris), tout en respectant la charte bleue demandée pour ce module.
- **Workflow de Simulation** : Simplification de l'aperçu par retrait du bloc de validation finale pour permettre une navigation plus fluide.

### 3. Sécurité & Gouvernance
- **Exclusion Publique** : Les profils `Chauffeur` et `Superviseur` sont désormais invisibles sur le formulaire d'inscription public.
- **Validation Serveur** : Ajout d'une sécurité stricte dans `UserController` pour bloquer toute tentative d'inscription manuelle sur ces profils administratifs.

---
*Note : Ce fichier sert de journal de bord pour le suivi des évolutions majeures du projet.*
