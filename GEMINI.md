# Audit Terrain - Instructions de Sécurité

## 🚨 RÈGLES CRITIQUES (NE JAMAIS TRANSGRESSER)
1. **ZÉRO MIGRATE FRESH** : Interdiction absolue d'utiliser `php artisan migrate:fresh`. Cette commande détruit les données locales.
2. **AUTORISATION DE TEST** : Ne jamais lancer de suites de tests (`php artisan test` ou PHPUnit) sans l'autorisation explicite de l'utilisateur, car certains tests pourraient altérer la base de données.
3. **PERSISTENCE DES DONNÉES** : Toujours privilégier `php artisan migrate` simple pour les évolutions de schéma.

## 🔄 SYNCHRONISATION LISTE MAÎTRE (RÉCONCILIATION)
1. **RÉFÉRENCE ABSOLUE** : Le fichier Excel de la direction est la source de vérité administrative.
2. **ÉTATS DE VALIDATION** :
   - `officiel_inscrit` : Agent dans Excel + Profil déjà complété.
   - `officiel_attente` : Agent dans Excel + Profil vide (à compléter).
   - `reserve` : Inscription libre non présente dans la liste officielle.
3. **LOGIQUE DE MATCHING** :
   - Priorité 1 : Matricule ou Téléphone (Match exact).
   - Priorité 2 : Nom + Prénom (Match strict au ministère ou match partiel suggéré).
4. **GESTION DES PROFILS** :
   - Le profil Excel écrase le profil actuel pour le déploiement.
   - Le choix original de l'agent est TOUJOURS conservé dans `profil_initial_id` en cas de divergence.
   - **Règles de conversion** : `Auditeur` ➔ `Auditeur IT`, `Support/Administratif` ➔ `Auditeur Administratif`.

## Procédure de Restauration (en cas de perte)
Si la base est vide, utiliser uniquement les seeders ou les fichiers SQL fournis :
- `php artisan db:seed` (lance tous les seeders)
- Import manuel de `ministere.sql` si nécessaire.
