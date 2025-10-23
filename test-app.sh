#!/bin/bash

# 🧪 Script de Test Complet - Waste2Product Application
# Ce script teste toutes les fonctionnalités de l'application

set -e  # Arrêter si une commande échoue

echo "🚀 =========================================="
echo "   TEST COMPLET - WASTE2PRODUCT APPLICATION"
echo "=========================================="
echo ""

# Couleurs pour l'affichage
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour afficher les résultats
check_status() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ $1${NC}"
    else
        echo -e "${RED}❌ $1${NC}"
        exit 1
    fi
}

# 1. Vérifier que Docker tourne
echo "📦 1. Vérification Docker..."
docker --version > /dev/null 2>&1
check_status "Docker installé"

# 2. Build et démarrage des containers
echo ""
echo "🏗️  2. Démarrage des services Docker..."
docker-compose up -d
check_status "Containers démarrés"

# 3. Attendre que MySQL soit prêt
echo ""
echo "⏳ 3. Attente de MySQL (max 30s)..."
timeout=30
while [ $timeout -gt 0 ]; do
    if docker exec waste2product_db mysqladmin ping -h localhost -uroot -proot --silent 2>/dev/null; then
        check_status "MySQL prêt"
        break
    fi
    sleep 1
    ((timeout--))
done

if [ $timeout -eq 0 ]; then
    echo -e "${RED}❌ MySQL timeout${NC}"
    exit 1
fi

# 4. Vérifier les containers actifs
echo ""
echo "🔍 4. Vérification des containers..."
docker ps --format "table {{.Names}}\t{{.Status}}" | grep waste2product
check_status "Tous les containers actifs"

# 5. Tester la connexion à l'application
echo ""
echo "🌐 5. Test de connectivité HTTP..."
sleep 5  # Attendre que nginx soit prêt
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200\|302"
check_status "Application accessible sur http://localhost:8080"

# 6. Tester la base de données
echo ""
echo "🗄️  6. Test de la base de données..."
docker exec waste2product_db mysql -uroot -proot -e "SHOW DATABASES;" | grep -q waste2product
check_status "Base de données 'waste2product' existe"

# 7. Vérifier les migrations (si déjà lancées)
echo ""
echo "📊 7. Vérification des tables..."
TABLE_COUNT=$(docker exec waste2product_db mysql -uroot -proot waste2product -e "SHOW TABLES;" 2>/dev/null | wc -l)
if [ $TABLE_COUNT -gt 1 ]; then
    echo -e "${GREEN}✅ Tables détectées: $TABLE_COUNT tables${NC}"
else
    echo -e "${YELLOW}⚠️  Aucune table - Migrations non exécutées${NC}"
fi

# 8. Tester PHP dans le container
echo ""
echo "🐘 8. Test PHP..."
docker exec waste2product_app php -v | grep -q "PHP 8.2"
check_status "PHP 8.2 installé"

# 9. Vérifier Composer
echo ""
echo "📦 9. Test Composer..."
docker exec waste2product_app composer --version > /dev/null 2>&1
check_status "Composer disponible"

# 10. Vérifier les permissions storage
echo ""
echo "📁 10. Test permissions storage..."
docker exec waste2product_app test -w /var/www/html/storage
check_status "Dossier storage accessible en écriture"

# 11. Tester Prometheus
echo ""
echo "📈 11. Test Prometheus..."
curl -s http://localhost:9090/-/healthy | grep -q "Prometheus"
check_status "Prometheus accessible sur http://localhost:9090"

# 12. Tester Grafana
echo ""
echo "📊 12. Test Grafana..."
curl -s -o /dev/null -w "%{http_code}" http://localhost:3000 | grep -q "200\|302"
check_status "Grafana accessible sur http://localhost:3000"

# 13. Afficher les logs récents
echo ""
echo "📋 13. Logs récents de l'application..."
docker-compose logs --tail=5 app

# Résumé final
echo ""
echo "=========================================="
echo "🎉 TESTS TERMINÉS AVEC SUCCÈS!"
echo "=========================================="
echo ""
echo "📌 Services disponibles:"
echo "   🌐 Application: http://localhost:8080"
echo "   🗄️  MySQL: localhost:3307"
echo "   📈 Prometheus: http://localhost:9090"
echo "   📊 Grafana: http://localhost:3000 (admin/admin)"
echo ""
echo "🔧 Commandes utiles:"
echo "   - Voir les logs: docker-compose logs -f app"
echo "   - Arrêter: docker-compose down"
echo "   - Redémarrer: docker-compose restart"
echo "   - Shell dans app: docker exec -it waste2product_app bash"
echo ""
