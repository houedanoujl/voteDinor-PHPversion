#!/bin/bash

# Script d'initialisation minimal pour le conteneur Laravel

set -e

echo "🚀 Démarrage minimal de l'application DINOR Laravel..."

# Créer les répertoires de logs s'ils n'existent pas
mkdir -p /var/www/storage/logs

# Générer la clé d'application si nécessaire
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --force || true
fi

echo "✅ Démarrage de PHP-FPM..."

# Démarrer PHP-FPM en mode foreground
exec php-fpm