# 📱 Guide de Test Mobile - DINOR

## 🚀 Démarrage Rapide

### 1. Lancer le script automatique
```bash
./start-mobile-testing.sh
```

### 2. Ou manuel :
```bash
# Découvrir votre IP
ifconfig | grep "inet " | grep -v 127.0.0.1

# Démarrer Docker
docker-compose up -d --build

# Vérifier l'accès
curl http://192.168.1.21:8080
```

## 📋 Tests à Effectuer

### 🍎 **iPhone (Safari/Chrome)**

#### Test 1: Navigation de base
- [ ] Ouvrir `http://192.168.1.21:8080`
- [ ] Vérifier que la page d'accueil se charge
- [ ] Navigation responsive fonctionne
- [ ] Boutons tactiles réactifs

#### Test 2: Upload photo HEIC
- [ ] Aller sur la page d'inscription candidat
- [ ] Prendre une photo avec l'appareil (format HEIC automatique)
- [ ] Upload de la photo
- [ ] Vérifier la conversion automatique HEIC → JPEG
- [ ] Vérifier l'optimisation (thumbnail créé)

#### Test 3: Performance
- [ ] Temps de chargement des images
- [ ] Lazy loading fonctionne
- [ ] Pas de rechargements multiples
- [ ] Navigation fluide

### 🤖 **Android (Chrome/Firefox)**

#### Test 1: Navigation
- [ ] Accès à `http://192.168.1.21:8080`
- [ ] Interface responsive adaptée
- [ ] Boutons et liens fonctionnels

#### Test 2: Upload photo standard
- [ ] Upload photo JPEG/PNG standard
- [ ] Vérification optimisation automatique
- [ ] Affichage correct des thumbnails

#### Test 3: Compatibilité
- [ ] Fonctionnement sur Chrome mobile
- [ ] Test sur Firefox mobile
- [ ] Gestion des différentes résolutions

## 🔧 Debug Mobile

### Logs en temps réel
```bash
# Logs application
docker-compose logs -f app

# Logs serveur web
docker-compose logs -f webserver

# Tous les logs
docker-compose logs -f
```

### Accès shell container
```bash
docker-compose exec app bash
```

### Vérifier les fichiers uploadés
```bash
ls -la storage/app/public/candidates/
```

### Tester l'optimisation manuelle
```bash
php artisan candidates:convert-heic --backup --update-db
```

## 📊 Points de Test Spécifiques

### Upload et Optimisation
1. **Fichier HEIC (iPhone)**
   - Original: `photo.heic` (préservé)
   - Converti: `photo_converted.jpg`
   - Thumbnail: `photo_thumb.jpg`
   - Principal: `photo_main.jpg`

2. **Fichier Standard (Android)**
   - Original: `photo.jpg`
   - Thumbnail: `photo_thumb.jpg`
   - Principal: `photo_main.jpg`

### Performance
- Temps de conversion HEIC < 5s
- Taille thumbnail < 50KB
- Lazy loading actif
- Pas d'erreurs console

### UX Mobile
- Interface tactile intuitive
- Retours visuels (loading, success)
- Messages d'erreur clairs
- Navigation fluide

## 🚨 Problèmes Courants

### 1. "Site non accessible"
- Vérifier que Mac et mobile sont sur le même WiFi
- Ping l'adresse : `ping 192.168.1.21`
- Vérifier firewall Mac
- Redémarrer Docker : `docker-compose down && docker-compose up -d`

### 2. "Upload échoue"
```bash
# Vérifier permissions
chmod -R 755 storage/app/public/candidates/

# Logs upload
docker-compose logs app | grep -i upload
```

### 3. "Images ne s'affichent pas"
```bash
# Vérifier symlink storage
docker-compose exec app php artisan storage:link

# Permissions images
chmod -R 644 storage/app/public/candidates/*
```

### 4. "HEIC non converti"
```bash
# Tester ImageMagick
docker-compose exec app convert -version
docker-compose exec app convert -list format | grep -i heic
```

## 📱 URLs de Test

- **Accueil**: `http://192.168.1.21:8080`
- **Inscription**: `http://192.168.1.21:8080/register`
- **Candidats**: `http://192.168.1.21:8080/candidates`
- **Admin**: `http://192.168.1.21:8080/admin`

## 🔄 Workflow de Test

1. **Setup** → Lancer `./start-mobile-testing.sh`
2. **iPhone** → Tester upload HEIC + navigation
3. **Android** → Tester upload standard + performance  
4. **Cross-platform** → Vérifier consistency
5. **Debug** → Logs et correction si nécessaire
6. **Cleanup** → `docker-compose down`

## ✅ Checklist Finale

- [ ] App accessible sur les 2 plateformes
- [ ] Upload photos fonctionne (HEIC + standard)
- [ ] Optimisation automatique active
- [ ] Performance acceptable
- [ ] UX mobile satisfaisante
- [ ] Logs propres (pas d'erreurs critiques)

---
**💡 Tip**: Gardez les outils de développement mobile ouverts (Safari → Développer → iPhone, Chrome → Inspecter)