# 📱 Guide de Résolution - Upload Mobile DINOR

## 🚨 Problème: "Une photo est obligatoire"

### ✅ Solutions Appliquées

#### 1. **Debug Avancé Ajouté**
- Logs détaillés dans `updatedPhoto()` et `submit()`
- Information mobile dans `LivewireUploadMiddleware`
- Debug visuel en mode local

#### 2. **Input File Amélioré**
```html
<input type="file" 
       accept="image/*,.heic,.heif,.webp,.jpg,.jpeg,.png,.gif" 
       capture="environment"  <!-- Force caméra arrière -->
       wire:model="photo">
```

#### 3. **Validation Plus Permissive**
```php
'photo' => 'required|file|mimes:jpeg,jpg,png,gif,webp,heic,heif|max:5120'
```

#### 4. **Script de Test Dédié**
- **URL**: `http://192.168.1.21:8080/debug-mobile-upload.php`
- Test direct sans Livewire
- Diagnostic complet du système

## 🔍 Diagnostic Étape par Étape

### 1. **Tester l'Upload Direct**
```bash
# Accéder au script de test
http://192.168.1.21:8080/debug-mobile-upload.php
```

### 2. **Vérifier les Logs en Temps Réel**
```bash
docker-compose logs -f app | grep -i "photo\|upload"
```

### 3. **Diagnostics Navigateur Mobile**
- **Safari iOS**: Réglages → Safari → Avancé → Inspecteur web
- **Chrome Android**: Menu → Plus d'outils → Outils de développement

### 4. **Console JavaScript**
- Ouvrir F12 → Console
- Chercher les erreurs Livewire
- Vérifier les requêtes `/livewire/upload-file`

## 🔧 Points de Vérification

### Configuration PHP
```ini
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 1024M
max_execution_time = 300
```

### Configuration Livewire
```php
// config/livewire.php
'temporary_file_upload' => [
    'rules' => ['required', 'file', 'max:51200'], // 50MB
    'preview_mimes' => [..., 'heic', 'heif'],
]
```

### Middleware Actif
```php
// app/Http/Middleware/LivewireUploadMiddleware.php
// Doit être enregistré dans le kernel
```

## 📊 Patterns de Logs à Surveiller

### ✅ Upload Réussi
```
📱 PHOTO UPDATED - Debug Mobile
📱 DÉTAILS PHOTO MOBILE: filename, size, mime
✅ Photo mobile validée avec succès
📱 LivewireUploadMiddleware applied: is_mobile=true
```

### ❌ Échec Upload
```
⚠️  Photo est null après update
❌ Erreur validation photo mobile
Aucune photo détectée lors de la soumission mobile
```

## 🛠️ Actions de Debug

### 1. **Vider le Cache**
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan config:clear
```

### 2. **Permissions Storage**
```bash
docker-compose exec app chmod -R 755 storage/
docker-compose exec app chown -R www-data:www-data storage/
```

### 3. **Test Manuel Livewire**
```php
// Dans une route de test
Route::post('/test-upload', function(Request $request) {
    Log::info('Test upload direct', [
        'has_files' => $request->hasFile('photo'),
        'files' => $request->allFiles(),
    ]);
    
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        Log::info('Fichier détecté', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]);
    }
    
    return response()->json(['success' => true]);
});
```

## 📱 Spécificités Mobiles

### iPhone (Safari)
- HEIC automatique pour photos nouvelles
- Nécessite `accept="image/*,.heic"`
- `capture="environment"` pour caméra arrière

### Android (Chrome)
- JPEG standard généralement
- Support variable selon navigateur
- `capture="camera"` alternatif

### Problèmes Courants
1. **Fichier trop volumineux**: Réduire qualité photo dans l'app
2. **Format non supporté**: Vérifier `accept` et `mimes`
3. **Timeout**: Augmenter limites PHP/nginx
4. **CORS**: Vérifier domaines autorisés

## 🚀 Test de Validation

### Checklist de Test
- [ ] `debug-mobile-upload.php` fonctionne
- [ ] Logs s'affichent dans `docker-compose logs`
- [ ] Upload direct PHP réussit
- [ ] Livewire détecte le fichier dans `updatedPhoto()`
- [ ] Validation passe dans `submit()`
- [ ] Fichier sauvegardé dans `storage/app/public/candidates/`
- [ ] Optimisation HEIC fonctionne

### Commandes de Debug
```bash
# Logs en temps réel
docker-compose logs -f app | grep "📱\|PHOTO\|upload"

# Tester upload direct
curl -X POST -F "test_photo=@photo.jpg" http://192.168.1.21:8080/debug-mobile-upload.php

# Vérifier fichiers uploadés
ls -la storage/app/public/candidates/
ls -la storage/app/public/livewire-tmp/
```

## 💡 Solutions de Derniers Recours

### 1. **Mode de Compatibilité**
```javascript
// Forcer rechargement après sélection
document.getElementById('photo').addEventListener('change', function() {
    setTimeout(() => window.Livewire.emit('refreshComponent'), 500);
});
```

### 2. **Upload Ajax Manuel**
```javascript
// Bypass Livewire pour upload critique
const formData = new FormData();
formData.append('photo', fileInput.files[0]);
fetch('/api/upload-photo', {method: 'POST', body: formData});
```

### 3. **Validation Côté Client**
```javascript
// Pré-validation avant Livewire
function validateFileBeforeUpload(file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/heic'];
    
    if (file.size > maxSize) {
        alert('Fichier trop volumineux (max 5MB)');
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        alert('Format non supporté');
        return false;
    }
    
    return true;
}
```

## 📞 Support Debug

En cas de problème persistant :
1. Collecter les logs complets
2. Tester `debug-mobile-upload.php`
3. Vérifier network tab du navigateur
4. Capturer erreurs JavaScript console

---
**🎯 L'objectif est que les uploads mobiles fonctionnent parfaitement, notamment les fichiers HEIC d'iPhone !**