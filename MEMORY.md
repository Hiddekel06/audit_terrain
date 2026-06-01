# Mémoire du Projet - Audit Terrain

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
