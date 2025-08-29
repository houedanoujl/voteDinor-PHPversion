#!/bin/bash

# Script d'initialisation robuste pour le conteneur Laravel

set -e

echo "🚀 Démarrage de l'application DINOR Laravel..."

# Créer les répertoires de logs s'ils n'existent pas
mkdir -p /var/www/storage/logs

# Générer la clé d'application si nécessaire
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --force || true
fi

# Attendre que MySQL soit disponible (avec timeout)
echo "⏳ Attente de la base de données..."
TIMEOUT=60
COUNTER=0

while ! php artisan migrate:status > /dev/null 2>&1; do
    echo "Base de données non disponible, attente 2 secondes... ($COUNTER/$TIMEOUT)"
    sleep 2
    COUNTER=$((COUNTER + 2))

    if [ $COUNTER -ge $TIMEOUT ]; then
        echo "⚠️ Timeout atteint, démarrage sans attendre la DB"
        break
    fi
done

if [ $COUNTER -lt $TIMEOUT ]; then
    echo "✅ Base de données connectée"

    # Exécuter les migrations
    echo "📊 Exécution des migrations..."
    php artisan migrate --force || echo "⚠️ Erreur lors des migrations, on continue..."

    # Nettoyer le cache
    echo "🧹 Nettoyage du cache..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "✅ Démarrage de PHP-FPM..."

# Démarrer PHP-FPM en mode foreground
exec php-fpm
