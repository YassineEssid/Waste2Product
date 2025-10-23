# 🧪 Script de Test Complet - Waste2Product Application
# Version PowerShell pour Windows

$ErrorActionPreference = "Stop"

Write-Host "🚀 ==========================================" -ForegroundColor Cyan
Write-Host "   TEST COMPLET - WASTE2PRODUCT APPLICATION" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

function Test-Step {
    param($Message, $Condition)
    if ($Condition) {
        Write-Host "✅ $Message" -ForegroundColor Green
    } else {
        Write-Host "❌ $Message" -ForegroundColor Red
        exit 1
    }
}

# 1. Vérifier Docker
Write-Host "📦 1. Vérification Docker..." -ForegroundColor Yellow
$dockerVersion = docker --version 2>$null
Test-Step "Docker installé" ($null -ne $dockerVersion)

# 2. Démarrer les containers
Write-Host ""
Write-Host "🏗️  2. Démarrage des services Docker..." -ForegroundColor Yellow
docker-compose up -d
Test-Step "Containers démarrés" ($LASTEXITCODE -eq 0)

# 3. Attendre MySQL
Write-Host ""
Write-Host "⏳ 3. Attente de MySQL (max 30s)..." -ForegroundColor Yellow
$timeout = 30
$mysqlReady = $false
while ($timeout -gt 0) {
    $result = docker exec waste2product_db mysqladmin ping -h localhost -uroot -proot --silent 2>$null
    if ($LASTEXITCODE -eq 0) {
        $mysqlReady = $true
        break
    }
    Start-Sleep -Seconds 1
    $timeout--
}
Test-Step "MySQL prêt" $mysqlReady

# 4. Vérifier les containers
Write-Host ""
Write-Host "🔍 4. Vérification des containers..." -ForegroundColor Yellow
docker ps --filter "name=waste2product" --format "table {{.Names}}\t{{.Status}}"
Test-Step "Tous les containers actifs" ($LASTEXITCODE -eq 0)

# 5. Tester l'application web
Write-Host ""
Write-Host "🌐 5. Test de connectivité HTTP..." -ForegroundColor Yellow
Start-Sleep -Seconds 5
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing -TimeoutSec 10
    Test-Step "Application accessible sur http://localhost:8080" ($response.StatusCode -in @(200, 302))
} catch {
    Test-Step "Application accessible" $false
}

# 6. Tester la base de données
Write-Host ""
Write-Host "🗄️  6. Test de la base de données..." -ForegroundColor Yellow
$dbCheck = docker exec waste2product_db mysql -uroot -proot -e "SHOW DATABASES;" 2>$null | Select-String "waste2product"
Test-Step "Base de données 'waste2product' existe" ($null -ne $dbCheck)

# 7. Compter les tables
Write-Host ""
Write-Host "📊 7. Vérification des tables..." -ForegroundColor Yellow
$tables = docker exec waste2product_db mysql -uroot -proot waste2product -e "SHOW TABLES;" 2>$null
if ($tables) {
    $tableCount = ($tables -split "`n").Count - 1
    Write-Host "✅ Tables détectées: $tableCount tables" -ForegroundColor Green
} else {
    Write-Host "⚠️  Aucune table - Migrations non exécutées" -ForegroundColor Yellow
}

# 8. Tester PHP
Write-Host ""
Write-Host "🐘 8. Test PHP..." -ForegroundColor Yellow
$phpVersion = docker exec waste2product_app php -v 2>$null | Select-String "PHP 8.2"
Test-Step "PHP 8.2 installé" ($null -ne $phpVersion)

# 9. Vérifier Composer
Write-Host ""
Write-Host "📦 9. Test Composer..." -ForegroundColor Yellow
$composerVersion = docker exec waste2product_app composer --version 2>$null
Test-Step "Composer disponible" ($null -ne $composerVersion)

# 10. Tester Prometheus
Write-Host ""
Write-Host "📈 10. Test Prometheus..." -ForegroundColor Yellow
try {
    $promResponse = Invoke-WebRequest -Uri "http://localhost:9090/-/healthy" -UseBasicParsing -TimeoutSec 5
    Test-Step "Prometheus accessible sur http://localhost:9090" ($promResponse.StatusCode -eq 200)
} catch {
    Write-Host "⚠️  Prometheus non accessible" -ForegroundColor Yellow
}

# 11. Tester Grafana
Write-Host ""
Write-Host "📊 11. Test Grafana..." -ForegroundColor Yellow
try {
    $grafanaResponse = Invoke-WebRequest -Uri "http://localhost:3000" -UseBasicParsing -TimeoutSec 5
    Test-Step "Grafana accessible sur http://localhost:3000" ($grafanaResponse.StatusCode -in @(200, 302))
} catch {
    Write-Host "⚠️  Grafana non accessible" -ForegroundColor Yellow
}

# 12. Logs récents
Write-Host ""
Write-Host "📋 12. Logs récents de l'application..." -ForegroundColor Yellow
docker-compose logs --tail=5 app

# Résumé final
Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "🎉 TESTS TERMINÉS AVEC SUCCÈS!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📌 Services disponibles:" -ForegroundColor Yellow
Write-Host "   🌐 Application: http://localhost:8080"
Write-Host "   🗄️  MySQL: localhost:3307"
Write-Host "   📈 Prometheus: http://localhost:9090"
Write-Host "   📊 Grafana: http://localhost:3000 (admin/admin)"
Write-Host ""
Write-Host "🔧 Commandes utiles:" -ForegroundColor Yellow
Write-Host "   - Voir les logs: docker-compose logs -f app"
Write-Host "   - Arrêter: docker-compose down"
Write-Host "   - Redémarrer: docker-compose restart"
Write-Host "   - Shell dans app: docker exec -it waste2product_app bash"
Write-Host ""

# Ouvrir automatiquement le navigateur
Write-Host "🌐 Ouverture de l'application dans le navigateur..." -ForegroundColor Cyan
Start-Process "http://localhost:8080"
