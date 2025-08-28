#!/bin/bash

# Script de test pour vérifier que le serveur fonctionne

echo "🧪 Test du serveur DINOR..."

# Vérifier que l'application répond
echo "🔍 Test de l'URL principale (localhost:8080)..."
if curl -f -s http://localhost:8080 > /dev/null; then
    echo "✅ L'application principale répond"
else
    echo "❌ L'application principale ne répond pas"
    echo "📊 Vérification des conteneurs Docker:"
    docker-compose ps
    echo ""
    echo "📋 Logs du serveur web:"
    docker-compose logs webserver --tail 10
    echo ""
    echo "📋 Logs de l'application:"
    docker-compose logs app --tail 10
    exit 1
fi

# Vérifier l'admin Filament
echo "🔍 Test de l'admin Filament (localhost:8080/admin)..."
if curl -f -s http://localhost:8080/admin > /dev/null; then
    echo "✅ L'admin Filament répond"
else
    echo "⚠️ L'admin Filament ne répond pas (peut être normal si pas configuré)"
fi

# Vérifier MailHog
echo "🔍 Test de MailHog (localhost:8025)..."
if curl -f -s http://localhost:8025 > /dev/null; then
    echo "✅ MailHog répond"
else
    echo "❌ MailHog ne répond pas"
fi

echo ""
echo "🎉 Tests terminés. Si tous les services répondent, votre environnement est fonctionnel !"
echo ""
echo "📌 URLs importantes:"
echo "   - Application: http://localhost:8080"
echo "   - Admin Panel: http://localhost:8080/admin" 
echo "   - Dashboard: http://localhost:8080/dashboard"
echo "   - MailHog: http://localhost:8025"