# Configuration Green API pour WhatsApp

## Variables d'environnement à ajouter dans votre fichier `.env`

```env
# Configuration Green API
GREEN_API_ID=votre_instance_id
GREEN_API_TOKEN=votre_token_access

# Exemples :
# GREEN_API_ID=1101234567
# GREEN_API_TOKEN=abcd1234efgh5678ijkl9012mnop3456qrst7890
```

## Comment obtenir ces informations

1. **Créez un compte sur Green API** : https://green-api.com/
2. **Créez une instance** dans votre console Green API
3. **Récupérez vos identifiants** :
   - `Instance ID` : ID numérique de votre instance
   - `Access Token` : Token d'accès pour l'API

## Configuration dans le fichier

Les variables sont déjà configurées dans `config/services.php` :

```php
'whatsapp' => [
    'green_api' => [
        'instance_id' => env('GREEN_API_ID'),
        'token' => env('GREEN_API_TOKEN'),
        'api_url' => env('GREEN_API_URL', 'https://api.green-api.com'),
    ],
    // ...
],
```

## Test de la configuration

Une fois configuré, vous pouvez tester via l'admin Filament :

1. Allez dans la liste des candidats
2. Cliquez sur le bouton "WhatsApp" d'un candidat
3. Le message sera envoyé via Green API

## Fonctionnalités disponibles

- ✅ **Envoi de messages individuels** via les boutons dans la liste des candidats
- ✅ **Messages automatiques** lors de l'approbation/rejet des candidats
- ✅ **Vérification du statut** de la connexion Green API
- ✅ **Gestion des erreurs** avec notifications visuelles
- ✅ **Interface moderne** avec boutons améliorés

## Logs et débogage

Les logs sont automatiquement enregistrés dans `storage/logs/laravel.log` pour :
- Envois de messages réussis
- Erreurs d'envoi
- Problèmes de configuration

## Sécurité

- Les tokens sont stockés dans les variables d'environnement
- Validation CSRF pour toutes les requêtes
- Middleware admin requis pour l'accès aux fonctions WhatsApp
