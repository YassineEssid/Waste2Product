# Script pour commit et push des changements GitHub Actions

Write-Host "`n🚀 PRÉPARATION DU COMMIT..." -ForegroundColor Cyan
Write-Host ""

# Vérifier l'état git
git status

Write-Host ""
Write-Host "📝 Fichiers modifiés :" -ForegroundColor Yellow
Write-Host "  - sonar-project.properties (nettoyé)" -ForegroundColor White
Write-Host "  - .github/workflows/tests.yml (self-hosted runner)" -ForegroundColor White
Write-Host "  - .github/workflows/ci-cd.yml (self-hosted runner)" -ForegroundColor White
Write-Host ""
Write-Host "➕ Nouveau fichier :" -ForegroundColor Green
Write-Host "  - GITHUB_ACTIONS_LOCAL_SONAR.md" -ForegroundColor White
Write-Host ""
Write-Host "❌ Fichiers supprimés :" -ForegroundColor Red
Write-Host "  - GITHUB_ACTIONS_SETUP.md" -ForegroundColor White
Write-Host "  - QUICKSTART_CI.md" -ForegroundColor White
Write-Host ""

# Demander confirmation
$confirm = Read-Host "Voulez-vous commiter ces changements ? (O/N)"

if ($confirm -eq "O" -or $confirm -eq "o" -or $confirm -eq "Y" -or $confirm -eq "y") {
    Write-Host "`n✅ Ajout des fichiers..." -ForegroundColor Green
    
    # Ajouter les fichiers
    git add sonar-project.properties
    git add .github/workflows/tests.yml
    git add .github/workflows/ci-cd.yml
    git add GITHUB_ACTIONS_LOCAL_SONAR.md
    
    # Supprimer les fichiers du git (si existants)
    git rm GITHUB_ACTIONS_SETUP.md -f 2>$null
    git rm QUICKSTART_CI.md -f 2>$null
    
    Write-Host "✅ Commit des changements..." -ForegroundColor Green
    git commit -m "ci: Configure GitHub Actions with self-hosted runner for local SonarQube

- Remove SonarCloud guides (GITHUB_ACTIONS_SETUP.md, QUICKSTART_CI.md)
- Clean sonar-project.properties (remove hardcoded token/URL)
- Update workflows to use self-hosted runner for SonarQube job
- Add GITHUB_ACTIONS_LOCAL_SONAR.md guide for local setup
- Fix migrations (remove duplicate/broken migrations)"
    
    Write-Host ""
    Write-Host "🎯 PROCHAINE ÉTAPE:" -ForegroundColor Yellow
    Write-Host "  Exécutez : git push origin main" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "⚠️  AVANT DE PUSHER, VÉRIFIEZ:" -ForegroundColor Yellow
    Write-Host "  1. Runner auto-hébergé est actif" -ForegroundColor White
    Write-Host "  2. Secrets GitHub configurés:" -ForegroundColor White
    Write-Host "     - SONAR_TOKEN" -ForegroundColor Cyan
    Write-Host "     - SONAR_HOST_URL (ex: http://localhost:9000)" -ForegroundColor Cyan
    Write-Host ""
    
    # Proposer de pusher
    $push = Read-Host "Voulez-vous pusher maintenant ? (O/N)"
    if ($push -eq "O" -or $push -eq "o" -or $push -eq "Y" -or $push -eq "y") {
        Write-Host "`n🚀 Push vers GitHub..." -ForegroundColor Cyan
        git push origin main
        
        Write-Host ""
        Write-Host "✅ TERMINÉ !" -ForegroundColor Green
        Write-Host "🔍 Consultez GitHub Actions : https://github.com/YassineEssid/Waste2Product/actions" -ForegroundColor Cyan
        Write-Host ""
    } else {
        Write-Host "`n⏸️  Push annulé. Exécutez manuellement : git push origin main" -ForegroundColor Yellow
        Write-Host ""
    }
} else {
    Write-Host "`n❌ Commit annulé" -ForegroundColor Red
    Write-Host ""
}
