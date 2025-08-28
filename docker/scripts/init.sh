#!/bin/bash

# Script d'initialisation pour le conteneur Laravel

set -e

echo "🚀 Démarrage de l'application DINOR Laravel..."

# Attendre que MySQL soit disponible
echo "⏳ Attente de la base de données..."
until php artisan migrate:status >/dev/null 2>&1; do
    echo "Base de données non disponible, attente 2 secondes..."
    sleep 2
done

echo "✅ Base de données connectée"

# Exécuter les migrations
echo "📊 Exécution des migrations..."
php artisan migrate --force

# Effacer le cache
echo "🧹 Nettoyage du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien symbolique pour le storage si nécessaire
if [ ! -L /var/www/public/storage ]; then
    echo "🔗 Création du lien symbolique storage..."
    php artisan storage:link
fi

# Générer la clé d'application si nécessaire
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --force
fi

# Publier les assets Filament si nécessaire
echo "📦 Publication des assets Filament..."
php artisan filament:install --panels || true

# Créer un utilisateur admin par défaut si nécessaire
echo "👤 Vérification de l'utilisateur admin..."
php artisan tinker --execute="
if (!\App\Models\User::where('email', 'jeanluc@bigfiveabidjan.com')->exists()) {
    \App\Models\User::create([
        'name' => 'Jean-Luc Admin',
        'email' => 'jeanluc@bigfiveabidjan.com',
        'password' => bcrypt('admin2025!'),
        'email_verified_at' => now(),
        'is_admin' => true
    ]);
    echo 'Utilisateur admin créé: jeanluc@bigfiveabidjan.com / admin2025!' . PHP_EOL;
} else {
    echo 'Utilisateur admin existe déjà' . PHP_EOL;
}
" || true

# Démarrer Supervisor en arrière-plan (pour les queues)
echo "🔄 Démarrage des processus background..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &

# Créer les répertoires de logs s'ils n'existent pas
mkdir -p /var/www/storage/logs

echo "✅ Initialisation terminée, démarrage de PHP-FPM..."

# Démarrer PHP-FPM en mode foreground
exec php-fpm