# Script pour corriger les permissions du cache Laravel

Write-Host "=== Correction des permissions bootstrap/cache ===" -ForegroundColor Cyan

# 1. Arrêter tous les processus PHP
Write-Host "`n1. Arrêt des serveurs PHP existants..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
Start-Sleep -Seconds 1

# 2. Supprimer et recréer le dossier cache
Write-Host "2. Recréation du dossier cache..." -ForegroundColor Yellow
if (Test-Path "bootstrap\cache") {
    Remove-Item "bootstrap\cache" -Recurse -Force -ErrorAction SilentlyContinue
}
New-Item -ItemType Directory -Path "bootstrap\cache" -Force | Out-Null

# 3. Donner les permissions complètes
Write-Host "3. Application des permissions..." -ForegroundColor Yellow
$acl = Get-Acl "bootstrap\cache"
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule(
    "Everyone",
    "FullControl",
    "ContainerInherit,ObjectInherit",
    "None",
    "Allow"
)
$acl.SetAccessRule($accessRule)
Set-Acl "bootstrap\cache" $acl

# 4. Créer les fichiers de cache avec permissions
Write-Host "4. Création des fichiers de cache..." -ForegroundColor Yellow

$packagesContent = @'
<?php return [];
'@

$servicesContent = @'
<?php return [];
'@

Set-Content -Path "bootstrap\cache\packages.php" -Value $packagesContent -Encoding UTF8 -Force
Set-Content -Path "bootstrap\cache\services.php" -Value $servicesContent -Encoding UTF8 -Force

# Donner permissions aux fichiers aussi
$acl = Get-Acl "bootstrap\cache\packages.php"
$acl.SetAccessRule($accessRule)
Set-Acl "bootstrap\cache\packages.php" $acl

$acl = Get-Acl "bootstrap\cache\services.php"
$acl.SetAccessRule($accessRule)
Set-Acl "bootstrap\cache\services.php" $acl

# 5. Vérifier les permissions
Write-Host "`n5. Vérification..." -ForegroundColor Yellow
if (Test-Path "bootstrap\cache\packages.php") {
    Write-Host "   [OK] packages.php créé" -ForegroundColor Green
} else {
    Write-Host "   [ERREUR] packages.php manquant" -ForegroundColor Red
}

if (Test-Path "bootstrap\cache\services.php") {
    Write-Host "   [OK] services.php créé" -ForegroundColor Green
} else {
    Write-Host "   [ERREUR] services.php manquant" -ForegroundColor Red
}

# 6. Lancer le serveur
Write-Host "`n6. Démarrage du serveur..." -ForegroundColor Yellow
Write-Host "   URL: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "   Appuyez sur Ctrl+C pour arrêter`n" -ForegroundColor Gray

php -S 127.0.0.1:8000 -t public
