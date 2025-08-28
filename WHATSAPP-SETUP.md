# 📱 Configuration WhatsApp - Guide d'Administration

## 🎯 Vue d'ensemble

Le système DINOR intègre deux providers WhatsApp pour l'envoi de messages automatiques :
- **WhatsApp Business API** (Meta/Facebook) - Solution officielle
- **Green API** - Alternative plus simple

## 🔧 Configuration des Variables d'Environnement

### WhatsApp Business API (Recommandé)

Ajoutez ces variables dans votre fichier `.env` :

```env
# WhatsApp Business API Configuration
WHATSAPP_PROVIDER=business_api
WHATSAPP_PHONE_NUMBER_ID=votre_phone_number_id
WHATSAPP_ACCESS_TOKEN=votre_access_token
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
WHATSAPP_VERIFY_TOKEN=votre_verify_token
```

### Green API (Alternative)

```env
# Green API Configuration
WHATSAPP_PROVIDER=green_api
GREEN_API_ID=votre_instance_id
GREEN_API_TOKEN=votre_api_token
GREEN_API_URL=https://api.green-api.com
```

## 📋 Étapes de Configuration

### 1. WhatsApp Business API

1. **Créer un compte Meta Business**
   - Allez sur [Meta Business](https://business.facebook.com)
   - Créez un compte Business si vous n'en avez pas

2. **Configurer WhatsApp Business**
   - Dans Meta Business, allez dans "Tous les outils" > "WhatsApp"
   - Suivez les étapes pour configurer votre numéro de téléphone

3. **Obtenir les credentials**
   - Phone Number ID : Trouvé dans les paramètres WhatsApp
   - Access Token : Généré dans les paramètres de l'application

### 2. Green API

1. **Créer un compte Green API**
   - Allez sur [Green API](https://green-api.com)
   - Créez un compte gratuit

2. **Configurer une instance**
   - Créez une nouvelle instance WhatsApp
   - Scannez le QR code avec votre téléphone

3. **Obtenir les credentials**
   - Instance ID : Affiché dans le dashboard
   - API Token : Généré automatiquement

## 🧪 Test de la Configuration

### Via l'Interface Admin

1. **Page de Test WhatsApp**
   - Accédez à `/admin/whatsapp-test`
   - Sélectionnez le provider à tester
   - Entrez un numéro de test
   - Cliquez sur "Envoyer Test WhatsApp"

2. **Test depuis la Liste des Candidats**
   - Allez dans `/admin/candidates`
   - Cliquez sur "Test WhatsApp" pour un candidat
   - Le message sera envoyé au numéro du candidat

### Messages de Test Disponibles

- **Message d'approbation** : Notification de candidature approuvée
- **Message de rejet** : Notification de candidature rejetée
- **Message personnalisé** : Texte libre

## 🔍 Dépannage

### Erreurs Courantes

1. **"Configuration incomplète"**
   - Vérifiez que toutes les variables d'environnement sont définies
   - Redémarrez l'application après modification du `.env`

2. **"Token invalide"**
   - Vérifiez que le token d'accès est correct
   - Assurez-vous que le token n'a pas expiré

3. **"Numéro non autorisé"**
   - Pour WhatsApp Business API : Le numéro doit être vérifié
   - Pour Green API : Le numéro doit être dans la liste des contacts

### Logs de Debug

Les tentatives d'envoi sont loggées dans `storage/logs/laravel.log` :

```bash
tail -f storage/logs/laravel.log | grep WhatsApp
```

## 📞 Support

### WhatsApp Business API
- [Documentation officielle](https://developers.facebook.com/docs/whatsapp)
- [Support Meta](https://developers.facebook.com/support/)

### Green API
- [Documentation Green API](https://green-api.com/docs/)
- [Support Green API](https://green-api.com/support/)

## 🚀 Production

### Recommandations

1. **WhatsApp Business API** pour la production
   - Plus stable et fiable
   - Support officiel Meta
   - Limites d'envoi plus élevées

2. **Green API** pour le développement/test
   - Configuration plus simple
   - Gratuit pour les tests
   - Limites d'envoi plus basses

### Monitoring

- Surveillez les logs d'envoi
- Configurez des alertes en cas d'échec
- Testez régulièrement la configuration

---

**Note** : Ce guide est spécifique au système DINOR. Pour des questions générales sur WhatsApp API, consultez la documentation officielle des providers.
