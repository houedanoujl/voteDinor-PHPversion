# Optimisation Automatique des Images - Documentation

## 🎯 Vue d'ensemble

Le système d'optimisation automatique des images garantit que **chaque image uploadée par un candidat est automatiquement optimisée** lors de l'inscription, sans intervention manuelle.

## 🔄 Processus automatique

### 1. **Upload candidat (Nouveau)**
```
Candidat remplit le formulaire + upload photo
       ↓
Livewire CandidateRegistrationForm::submit()
       ↓
Image stockée dans storage/app/public/candidates/
       ↓
Événement CandidatePhotoUploaded dispatché (asynchrone)
       ↓
Listener OptimizeCandidatePhoto traite l'optimisation
       ↓
Génération automatique :
   • image_main.jpg (800x600, ~100KB)
   • image_thumb.jpg (400x300, ~50KB)  
   • image_small.jpg (150x150, ~20KB)
   • image_main.webp (version WebP)
   • image_thumb.webp (version WebP)
```

### 2. **Tracking du statut**
Chaque candidat a un statut d'optimisation dans la BDD :
- `pending` : En attente
- `processing` : En cours de traitement  
- `completed` : Optimisation réussie
- `failed` : Échec (avec message d'erreur)

## 🛠️ Fichiers du système

### **Composants principaux :**
- `app/Livewire/CandidateRegistrationForm.php` - Déclenche l'optimisation
- `app/Events/CandidatePhotoUploaded.php` - Événement d'upload  
- `app/Listeners/OptimizeCandidatePhoto.php` - Traitement asynchrone
- `app/Services/ImageOptimizationService.php` - Service d'optimisation

### **Commandes artisan :**
- `php artisan candidates:optimize-images` - Optimise les candidats existants
- `php artisan images:optimize` - Optimise toutes les images du dossier

### **Migration :**
- `database/migrations/*_add_photo_optimization_to_candidates.php`

## 📋 Commandes disponibles

### **Optimiser les candidats existants :**
```bash
# Optimiser les candidats non-optimisés (max 10)
php artisan candidates:optimize-images

# Optimiser plus de candidats
php artisan candidates:optimize-images --limit=50

# Forcer la re-optimisation de tous
php artisan candidates:optimize-images --force
```

### **Optimiser toutes les images d'un dossier :**
```bash
# Optimiser toutes les images candidates
php artisan images:optimize --directory=candidates
```

## 🔧 Configuration requise

### **Extensions PHP :**
- GD ou Imagick (pour manipulation d'images)
- Fileinfo (détection type MIME)

### **Queues (Recommandé) :**
Pour traitement asynchrone en production :
```bash
# Démarrer le worker des queues
php artisan queue:work

# Ou avec supervision
php artisan queue:work --daemon
```

### **Permissions :**
```bash
# Vérifier les permissions storage
chmod -R 755 storage/app/public/candidates/
```

## 📊 Monitoring et Logs

### **Logs automatiques :**
Tous les événements sont loggés dans `storage/logs/laravel.log` :
```
[INFO] Optimisation photo candidat - Début (candidate_id: 123)
[INFO] Images optimisées créées (5 versions créées)
[INFO] Optimisation photo candidat - Succès
```

### **Vérification d'un candidat :**
```php
$candidate = Candidate::find(1);

// Vérifier le statut
echo $candidate->optimization_status; // "Terminé"

// Vérifier si optimisées existent
$candidate->hasOptimizedImages(); // true/false

// Déclencher manuellement
$candidate->optimizeImages();
```

### **Statistiques :**
```bash
# Candidats par statut d'optimisation
SELECT photo_optimization_status, COUNT(*) 
FROM candidates 
WHERE photo_url IS NOT NULL 
GROUP BY photo_optimization_status;
```

## 🚨 Gestion d'erreurs

### **Erreurs communes :**
1. **Extension manquante :** `GD extension not found`
   → Installer php-gd : `sudo apt install php-gd`

2. **Fichier non trouvé :** `File not found for optimization`
   → Vérifier les chemins storage et permissions

3. **Mémoire insuffisante :** `Allowed memory size exceeded`
   → Augmenter `memory_limit` dans php.ini

### **Recovery :**
```bash
# Re-optimiser les candidats en échec
php artisan candidates:optimize-images --force

# Vérifier les logs
tail -f storage/logs/laravel.log | grep "Optimisation"
```

## 🎯 Résultats attendus

### **Performance :**
- **Images originales :** 500-650KB chacune
- **Images optimisées :** 50-100KB chacune  
- **Gain :** 80-85% de réduction
- **Chargement :** 5x plus rapide

### **UX :**
- Lazy loading fluide avec placeholders
- Support WebP pour navigateurs compatibles
- Fallback automatique vers JPEG
- Chargement progressif par batch de 12

## 🔄 Workflow complet

### **1. Nouveau candidat s'inscrit :**
```
Upload → Storage → Event → Queue → Optimisation → Statut "completed"
```

### **2. Affichage sur le site :**
```
Galerie → Lazy loading → Essai WebP → Fallback JPEG → Affichage
```

### **3. Monitoring :**
```
Admin → Logs → Statuts BDD → Métriques performance
```

## ⚡ Avantages du système

✅ **Automatique :** Aucune intervention manuelle requise  
✅ **Asynchrone :** N'impacte pas le temps d'inscription  
✅ **Robuste :** Gestion d'erreurs et retry automatique  
✅ **Scalable :** Compatible avec queues et workers multiples  
✅ **Trackable :** Logs complets et statuts en BDD  
✅ **Performant :** Réduction drastique des tailles d'images  

Le système garantit que **100% des nouveaux candidats** auront leurs images automatiquement optimisées pour des performances maximales ! 🚀