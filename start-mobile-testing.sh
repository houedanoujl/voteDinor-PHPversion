#!/bin/bash

# Script pour démarrer l'app en mode test mobile
echo "🚀 DÉMARRAGE TEST MOBILE - DINOR"
echo "================================="

# 1. Obtenir l'IP locale
LOCAL_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)
echo "📍 IP locale détectée: $LOCAL_IP"

# 2. Mettre à jour l'APP_URL si nécessaire
if grep -q "APP_URL=http://localhost:8080" .env; then
    echo "🔧 Mise à jour de APP_URL dans .env..."
    sed -i '' "s|APP_URL=http://localhost:8080|APP_URL=http://$LOCAL_IP:8080|g" .env
fi

if grep -q "APP_URL=http://127.0.0.1:8080" .env; then
    echo "🔧 Mise à jour de APP_URL dans .env..."
    sed -i '' "s|APP_URL=http://127.0.0.1:8080|APP_URL=http://$LOCAL_IP:8080|g" .env
fi

echo "✅ Configuration mise à jour"

# 3. Vérifier que Docker est démarré
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker n'est pas démarré. Veuillez démarrer Docker Desktop."
    exit 1
fi

# 4. Construire et démarrer les conteneurs
echo "🔨 Construction et démarrage des conteneurs..."
docker-compose down
docker-compose up -d --build

# 5. Attendre que les services soient prêts
echo "⏳ Attente du démarrage des services..."
sleep 10

# 6. Vérifier que l'app est accessible
echo "🔍 Vérification de l'accès à l'application..."
if curl -s "http://$LOCAL_IP:8080" > /dev/null; then
    echo "✅ Application accessible sur http://$LOCAL_IP:8080"
else
    echo "⚠️  Application non accessible, vérifiez les logs Docker"
    docker-compose logs --tail=20 webserver
fi

# 7. Afficher les informations de connexion
echo ""
echo "📱 CONNEXION MOBILE"
echo "==================="
echo "🔗 URL principale: http://$LOCAL_IP:8080"
echo "🎯 QR Code à scanner avec vos mobiles:"
echo ""

# Générer un QR code simple (si qrencode est installé)
if command -v qrencode > /dev/null; then
    qrencode -t UTF8 "http://$LOCAL_IP:8080"
else
    echo "💡 Installez qrencode pour générer un QR code:"
    echo "   brew install qrencode"
fi

echo ""
echo "📋 INSTRUCTIONS MOBILES:"
echo "========================"
echo "1. Connectez iPhone/Android au même WiFi"
echo "2. Ouvrez le navigateur mobile"
echo "3. Tapez: http://$LOCAL_IP:8080"
echo "4. Testez l'upload de photos HEIC (iPhone)"
echo ""
echo "🔧 DEBUGGING:"
echo "============="
echo "• Logs app: docker-compose logs -f app"
echo "• Logs web: docker-compose logs -f webserver" 
echo "• Accès shell: docker-compose exec app bash"
echo ""
echo "🛑 ARRÊT: docker-compose down"
echo ""
echo "🎉 Prêt pour les tests mobiles !"