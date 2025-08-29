#!/bin/bash

echo "🔄 Script de maintien en vie de PHP-FPM..."

while true; do
    # Vérifier si le site répond
    if curl -s -I http://localhost:8080 | grep -q "200 OK"; then
        echo "✅ $(date) - Site opérationnel"
    else
        echo "❌ $(date) - Site en panne, redémarrage de PHP-FPM..."
        docker-compose exec app pkill php-fpm 2>/dev/null || true
        sleep 2
        docker-compose exec app php-fpm -D
        echo "🔄 PHP-FPM redémarré"
    fi

    sleep 10
done
