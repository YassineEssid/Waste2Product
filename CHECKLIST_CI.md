# ✅ Checklist GitHub Actions + SonarQube Local

## Avant de pusher

- [ ] **Runner auto-hébergé actif**
  - Allez sur GitHub → Settings → Actions → Runners
  - Vérifiez qu'il est "Idle" ou "Active"

- [ ] **Secrets GitHub configurés**
  - GitHub → Settings → Secrets and variables → Actions
  - `SONAR_TOKEN` : Token de votre SonarQube local
  - `SONAR_HOST_URL` : `http://localhost:9000`

- [ ] **SonarQube local en cours d'exécution**
  ```bash
  docker-compose ps
  # Vérifiez que sonarqube est UP
  ```

- [ ] **SonarQube accessible**
  - Ouvrez http://localhost:9000
  - Login: admin / admin
  - Créez le projet "waste2product" si pas encore fait

## Commandes de commit

```bash
# Option 1: Script automatique
.\git-commit-ci.ps1

# Option 2: Manuellement
git add .
git commit -m "ci: Configure GitHub Actions with local SonarQube"
git push origin main
```

## Après le push

1. **Vérifier GitHub Actions**
   - https://github.com/YassineEssid/Waste2Product/actions
   - Le workflow devrait démarrer automatiquement

2. **Vérifier les jobs**
   - ✅ Job "tests" : Exécuté sur runner GitHub
   - ✅ Job "sonarqube" : Exécuté sur votre runner local
   - ✅ Job "code-quality" : Exécuté sur runner GitHub

3. **Consulter SonarQube**
   - http://localhost:9000
   - Projet : Waste2Product
   - Vérifier les résultats d'analyse

## En cas de problème

### ❌ "No runner available"
- Vérifiez que le runner est actif
- Redémarrez le runner si nécessaire

### ❌ "Unable to connect to SonarQube"
- Vérifiez `SONAR_HOST_URL` dans les secrets
- Vérifiez que SonarQube est démarré
- Vérifiez que le runner peut accéder à localhost:9000

### ❌ "Unauthorized"
- Vérifiez `SONAR_TOKEN` dans les secrets
- Régénérez un token dans SonarQube si nécessaire

## Documentation

📖 Guide complet : `GITHUB_ACTIONS_LOCAL_SONAR.md`
