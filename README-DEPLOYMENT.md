# Guide de Déploiement - Concours Photo DINOR

## 🚀 Démarrage rapide

### 1. Lancement de l'environnement Docker

```bash
docker-compose up -d
```

Les services seront disponibles aux adresses suivantes :
- **Application** : http://localhost:8080
- **Admin Panel** : http://localhost:8080/admin  
- **MailHog (Email Debug)** : http://localhost:8025
- **Base de données MySQL** : localhost:3306

### 2. Initialisation de la base de données

Une fois Docker lancé, exécutez dans un nouveau terminal :

```bash
# Installer les dépendances et migrer
docker-compose exec app composer install
docker-compose exec app php artisan migrate

# Charger les données d'exemple  
docker-compose exec app php artisan db:seed
```

## 🔐 Accès Admin

**URL** : http://localhost:8080/admin

**Identifiants :**
- **Email** : jeanluc@bigfiveabidjan.com  
- **Mot de passe** : admin2025!

### Fonctionnalités Admin
- Gestion des candidats (validation/rejet)
- Consultation des votes
- Statistiques du concours
- Envoi d'emails de notification

## 👥 Accès Utilisateur

**URL** : http://localhost:8080

### Fonctionnalités Utilisateur
- **Connexion** : Google OAuth ou Facebook OAuth
- **Inscription** : Formulaire candidat avec photo
- **Vote** : 1 vote par candidat par jour par utilisateur
- **Galerie** : Visualisation de tous les candidats approuvés

## 📧 Configuration Email

Les emails sont configurés pour fonctionner avec MailHog en développement.

**Accès MailHog** : http://localhost:8025
- Tous les emails envoyés par l'application sont interceptés
- Testez les notifications de candidature et d'approbation

### Configuration Production
Pour la production, modifiez `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 🔧 Services Docker

| Service   | Port | Description                    |
|-----------|------|--------------------------------|
| app       | 9000 | PHP-FPM (Laravel)             |
| webserver | 8080 | Nginx (Web server)             |  
| mysql     | 3306 | Base de données MySQL          |
| redis     | 6379 | Cache et sessions              |
| node      | 3000 | Vite (Assets build)            |
| mailhog   | 8025 | Debug email server             |

## 🎯 Tests de Fonctionnement

### 1. Test du vote
1. Allez sur http://localhost:8080
2. Cliquez "Participer avec Google" 
3. Votez pour un candidat
4. Vérifiez que le compteur s'incrémente
5. Essayez de revoter → Message "déjà voté"

### 2. Test d'inscription candidat  
1. Connectez-vous avec Google
2. Cliquez "Participer au concours"
3. Remplissez le formulaire avec photo
4. Vérifiez l'email dans MailHog
5. Validez depuis l'admin

### 3. Test Admin
1. Accédez à http://localhost:8080/admin
2. Connectez-vous avec les identifiants admin
3. Gérez les candidats (approuver/rejeter)
4. Consultez les statistiques

## 🔄 Commandes Utiles

```bash
# Voir les logs Laravel
docker-compose exec app php artisan pail

# Vider les caches  
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Générer nouvelle clé app
docker-compose exec app php artisan key:generate

# Créer un utilisateur admin
docker-compose exec app php artisan make:user

# Redémarrer les queues
docker-compose exec app php artisan queue:restart
```

## 🐛 Résolution de Problèmes

### Problème : "Connection refused MySQL"
**Solution** : Attendez que MySQL soit complètement démarré (30-60 secondes)

### Problème : "Permission denied" sur storage
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Problème : Assets non compilés
```bash
docker-compose exec node npm run build
```

### Problème : Emails non envoyés  
1. Vérifiez MailHog : http://localhost:8025
2. Consultez les logs : `docker-compose logs app`
3. Vérifiez les queues : `docker-compose exec app php artisan queue:work`

## 📊 Données d'Exemple

Le seeder crée automatiquement :
- **6 candidats** (5 approuvés, 1 en attente)
- **1 utilisateur admin** 
- **Photos** depuis Unsplash avec visages réels

### Candidats d'Exemple
1. **Adjoua Kouassi** - Cuisine traditionnelle
2. **Koffi Assouan** - Chef professionnel  
3. **Fatou Traoré** - Saveurs authentiques
4. **Moussa Diabaté** - Étudiant restauration
5. **Aminata Koné** - Bloggeuse culinaire
6. **Ibrahim Ouattara** - Restaurateur (en attente)

## ✅ Checklist de Déploiement

- [ ] Docker démarré : `docker-compose up -d`
- [ ] Migrations : `php artisan migrate`  
- [ ] Seeders : `php artisan db:seed`
- [ ] Application accessible : http://localhost:8080
- [ ] Admin accessible : http://localhost:8080/admin
- [ ] OAuth Google/Facebook configuré
- [ ] MailHog fonctionnel : http://localhost:8025
- [ ] Test vote utilisateur
- [ ] Test inscription candidat
- [ ] Test gestion admin

## 🌍 Configuration Production

### Variables d'environnement importantes

Configurez dans `.env` :

```env
# Base application
APP_NAME="Concours Photo DINOR"
APP_URL=https://your-domain.com

# Google Analytics
GOOGLE_ANALYTICS_TRACKING_ID=UA-XXXXXXXXX-X
GOOGLE_ANALYTICS_MEASUREMENT_ID=G-XXXXXXXXXX

# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback

# Facebook OAuth  
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URI=https://your-domain.com/auth/facebook/callback

# reCAPTCHA (optionnel)
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key

# WhatsApp Green API (optionnel)
GREEN_API_ID=your-instance-id
GREEN_API_TOKEN=your-token
```

## 📊 Nouvelles fonctionnalités ajoutées

### 🖼️ Gestion des médias avec Spatie
- **Upload intelligent** : Redimensionnement automatique des images
- **Conversions** : Génération de vignettes (400x400) et medium (800x600)
- **Sécurité** : Validation des types de fichiers et taille max 2MB
- **Performance** : Optimisation automatique avec sharpening

### 📈 Google Analytics intégré
- **Suivi des visites** : Pages vues et temps passé
- **Événements personnalisés** :
  - `vote` : Quand un utilisateur vote
  - `registration` : Quand un candidat s'inscrit  
  - `login` : Connexions OAuth (Google/Facebook)
- **Segments utilisateurs** : Distingue visiteurs/connectés
- **Données enrichies** : ID utilisateur pour les connectés

### 🎨 Avatars génériques
- **6 avatars SVG** uniques avec palette DINOR
- **Remplacement Unsplash** : Plus de dépendance externe
- **Style cohérent** : Intégration parfaite au design vintage
- **Performance** : Fichiers légers et rapides à charger

---

🎉 **Votre concours photo DINOR est maintenant opérationnel !**