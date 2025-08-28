# 🐛 Guide de Résolution des Problèmes Docker

## Problème : `localhost:8080` ne répond pas

### Solutions étape par étape :

#### 1️⃣ Vérifier l'état des conteneurs
```bash
docker-compose ps
```
Tous les services doivent être `Up`.

#### 2️⃣ Redémarrer complètement l'environnement
```bash
# Arrêter tout
docker-compose down

# Reconstruire les images (force)
docker-compose build --no-cache

# Relancer
docker-compose up -d
```

#### 3️⃣ Vérifier les logs de chaque service
```bash
# Logs du serveur web Nginx
docker-compose logs webserver

# Logs de l'application Laravel
docker-compose logs app

# Logs du service Node.js
docker-compose logs node

# Logs MySQL
docker-compose logs mysql
```

#### 4️⃣ Test de connexion direct
```bash
# Tester la connexion au conteneur Nginx
curl -I http://localhost:8080

# Tester depuis l'intérieur du réseau Docker
docker-compose exec webserver curl -I http://localhost
```

#### 5️⃣ Vérifier la configuration Nginx
```bash
# Tester la configuration
docker-compose exec webserver nginx -t

# Recharger Nginx si nécessaire
docker-compose exec webserver nginx -s reload
```

---

## Problème : Node.js version trop ancienne

### ✅ **Solution : Mise à jour effectuée**

Le `docker-compose.yml` a été mis à jour pour utiliser `node:22-alpine`.

Si vous rencontrez encore des erreurs Node.js :

```bash
# Forcer la reconstruction du conteneur Node
docker-compose build --no-cache node

# Redémarrer uniquement le service Node
docker-compose restart node

# Vérifier la version
docker-compose exec node node --version
```

---

## Problème : Base de données inaccessible

### Solutions :

#### 1️⃣ Attendre le démarrage complet de MySQL
```bash
# MySQL peut prendre 30-60 secondes au premier démarrage
docker-compose logs mysql -f
```

#### 2️⃣ Vérifier la connexion
```bash
# Test de connexion depuis l'app
docker-compose exec app php artisan migrate:status

# Connexion directe à MySQL
docker-compose exec mysql mysql -u dinor_user -pdinor_password dinor_vote
```

#### 3️⃣ Réinitialiser la base de données
```bash
# Supprimer le volume de données
docker-compose down -v

# Recréer tout
docker-compose up -d
```

---

## Problème : Permissions sur les fichiers

### Solutions :

```bash
# Corriger les permissions du stockage
docker-compose exec app chmod -R 775 storage bootstrap/cache

# Corriger le propriétaire des fichiers
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache public/storage
```

---

## Problème : Assets non compilés (Vite)

### Solutions :

```bash
# Installer les dépendances Node
docker-compose exec node npm install

# Compiler les assets en mode développement
docker-compose exec node npm run dev

# Ou compiler pour la production
docker-compose exec node npm run build
```

---

## Script de Test Automatique

Utilisez le script de test intégré :

```bash
# Rendre le script exécutable
chmod +x docker/scripts/test-server.sh

# Exécuter les tests
./docker/scripts/test-server.sh
```

---

## Commandes de Maintenance

### Nettoyer Docker complètement
```bash
# Arrêter tous les conteneurs
docker-compose down

# Supprimer images, conteneurs et volumes
docker system prune -a --volumes

# Reconstruire tout depuis zéro
docker-compose build --no-cache
docker-compose up -d
```

### Réinitialiser uniquement la base de données
```bash
# Supprimer uniquement le volume DB
docker-compose down
docker volume rm votedinor_dbdata
docker-compose up -d
```

### Logs en temps réel
```bash
# Suivre tous les logs
docker-compose logs -f

# Suivre un service spécifique
docker-compose logs -f app
```

---

## Points de Contrôle

✅ **Vérifications importantes :**

1. **Port 8080 libre** : `netstat -tuln | grep 8080`
2. **Docker en marche** : `docker --version`
3. **Docker Compose version** : `docker-compose --version`
4. **Espace disque suffisant** : `df -h`
5. **Variables d'environnement** : Fichier `.env` présent et configuré

---

## Si tout échoue

### Solution de dernier recours :

```bash
# 1. Tout supprimer
docker-compose down -v --remove-orphans
docker system prune -a --volumes -f

# 2. Vérifier le fichier .env
cp .env.example .env

# 3. Reconstruire complètement
docker-compose build --no-cache --pull
docker-compose up -d

# 4. Attendre 2 minutes et tester
sleep 120
curl http://localhost:8080
```

---

## Support

Si le problème persiste :

1. **Collecter les informations** :
   ```bash
   docker-compose ps > debug-containers.txt
   docker-compose logs > debug-logs.txt
   docker system df > debug-space.txt
   ```

2. **Vérifier l'environnement** :
   - OS et version
   - Version Docker / Docker Compose
   - RAM et espace disque disponibles

3. **Partager les informations** pour obtenir de l'aide

---

🎯 **Objectif : Avoir `http://localhost:8080` qui affiche la page d'accueil du concours DINOR !**