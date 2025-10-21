# Waste2Product - Laravel Server Launcher
# PowerShell Script

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Waste2Product - Laravel Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Create cache directory
Write-Host "[1/3] Creating cache directories..." -ForegroundColor Yellow
$cacheDir = "bootstrap\cache"

if (Test-Path $cacheDir) {
    Write-Host "  → Cache directory exists" -ForegroundColor Green
} else {
    New-Item -ItemType Directory -Path $cacheDir -Force | Out-Null
    Write-Host "  → Cache directory created" -ForegroundColor Green
}

# Step 2: Create cache files
Write-Host "[2/3] Creating cache files..." -ForegroundColor Yellow

$packagesContent = "<?php return [];"
$servicesContent = "<?php return [];"

Set-Content -Path "$cacheDir\packages.php" -Value $packagesContent -NoNewline -Encoding UTF8
Set-Content -Path "$cacheDir\services.php" -Value $servicesContent -NoNewline -Encoding UTF8

Write-Host "  → packages.php created" -ForegroundColor Green
Write-Host "  → services.php created" -ForegroundColor Green

# Step 3: Start server
Write-Host "[3/3] Starting Laravel server..." -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host " Server URL: http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

# Start PHP built-in server
php -S 127.0.0.1:8000 -t public

Write-Host ""
Write-Host "Server stopped." -ForegroundColor Red
