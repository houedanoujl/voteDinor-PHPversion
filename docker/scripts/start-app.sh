#!/bin/bash

echo "🚀 Démarrage de l'application DINOR Laravel..."

# Attendre que MySQL soit disponible
echo "⏳ Attente de la base de données..."
while ! php artisan migrate:status > /dev/null 2>&1; do
    echo "Base de données non disponible, attente 2 secondes..."
    sleep 2
done

echo "✅ Base de données connectée"

# Exécuter les migrations
echo "📊 Exécution des migrations..."
php artisan migrate --force

# Nettoyer le cache
echo "🧹 Nettoyage du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan optimize

# Démarrer PHP-FPM en mode supervisé
echo "🔄 Démarrage de PHP-FPM..."

# Boucle infinie pour maintenir PHP-FPM en vie
while true; do
    # Démarrer PHP-FPM en arrière-plan
    php-fpm -F &
    FPM_PID=$!

    echo "✅ PHP-FPM démarré avec PID: $FPM_PID"

    # Attendre que le processus se termine
    wait $FPM_PID
    EXIT_CODE=$?

    echo "⚠️ PHP-FPM s'est arrêté avec le code: $EXIT_CODE"
    echo "🔄 Redémarrage de PHP-FPM dans 3 secondes..."
    sleep 3
done
