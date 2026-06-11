# Audit Terrain - Instructions de Sécurité

## 🚨 RÈGLES CRITIQUES (NE JAMAIS TRANSGRESSER)
1. **ZÉRO MIGRATE FRESH** : Interdiction absolue d'utiliser `php artisan migrate:fresh`. Cette commande détruit les données locales.
2. **AUTORISATION DE TEST** : Ne jamais lancer de suites de tests (`php artisan test` ou PHPUnit) sans l'autorisation explicite de l'utilisateur, car certains tests pourraient altérer la base de données.
3. **PERSISTENCE DES DONNÉES** : Toujours privilégier `php artisan migrate` simple pour les évolutions de schéma.

## Procédure de Restauration (en cas de perte)
Si la base est vide, utiliser uniquement les seeders ou les fichiers SQL fournis :
- `php artisan db:seed` (lance tous les seeders)
- Import manuel de `ministere.sql` si nécessaire.
