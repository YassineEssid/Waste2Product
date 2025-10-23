# 🚀 GitHub Actions avec SonarQube Local

## ✅ Configuration actuelle

Votre projet est configuré pour utiliser **GitHub Actions** avec votre **SonarQube local** (Docker).

### Prérequis validés ✓

- [x] Runner GitHub Actions auto-hébergé installé et enregistré
- [x] SonarQube local accessible sur `http://localhost:9000`
- [x] Secrets GitHub configurés :
  - `SONAR_TOKEN` : Token d'authentification SonarQube
  - `SONAR_HOST_URL` : `http://localhost:9000` (ou URL accessible depuis le runner)

---

## 📋 Workflows disponibles

### 1. **tests.yml** - Tests + SonarQube (Matrice PHP)
**Déclenchement** : Push ou PR sur `main` / `develop`

**Jobs** :
- ✅ **tests** : Exécute les tests sur PHP 8.2 et 8.3 (runner GitHub)
- 🔍 **sonarqube** : Analyse de code (runner auto-hébergé)
- 📊 **code-quality** : PHPStan + PHP_CodeSniffer (runner GitHub)

### 2. **ci-cd.yml** - Pipeline complet
**Déclenchement** : Push ou PR sur `main` / `develop`

**Jobs** :
- ✅ **tests** : Tests avec couverture MySQL (runner GitHub)
- 🔍 **sonarqube** : Analyse SonarQube (runner auto-hébergé)
- 🐳 **docker** : Build et push image Docker (seulement sur `main`)

---

## 🔧 Configuration SonarQube

### Fichier `sonar-project.properties`
```properties
sonar.projectKey=waste2product
sonar.projectName=Waste2Product
sonar.sources=app
sonar.tests=tests
sonar.php.coverage.reportPaths=coverage/clover.xml
sonar.exclusions=vendor/**,storage/**,node_modules/**,bootstrap/cache/**,public/**
```

**Note** : `sonar.host.url` et `sonar.login` sont automatiquement injectés via les secrets GitHub.

---

## 🎯 Workflow type

```
Push code → GitHub Actions
    ↓
1. Tests unitaires (runner GitHub)
    ↓
2. Génération coverage/clover.xml
    ↓
3. Upload artifact coverage
    ↓
4. Analyse SonarQube (runner auto-hébergé)
    ↓
5. Quality Gate Check
    ↓
✅ Merge autorisé si quality gate PASS
```

---

## 🛠️ Commandes utiles

### Vérifier le runner auto-hébergé
Sur votre machine :
```bash
# Vérifier l'état du runner
./run.sh status  # Linux/Mac
.\run.cmd status  # Windows
```

### Consulter SonarQube
```bash
# Accéder à SonarQube local
http://localhost:9000

# Login: admin / admin (changez le mot de passe!)
```

### Tester localement avant push
```bash
# Lancer les tests avec couverture
docker-compose exec app vendor/bin/phpunit --coverage-clover=coverage/clover.xml

# Analyser avec SonarQube (depuis votre machine)
docker run --rm --network=waste2product_default \
  -e SONAR_HOST_URL=http://sonarqube:9000 \
  -e SONAR_LOGIN=YOUR_TOKEN \
  -v "${PWD}:/usr/src" \
  sonarsource/sonar-scanner-cli
```

---

## 📊 Vérifier les résultats

### 1. Sur GitHub
- Allez dans **Actions** → Cliquez sur votre workflow
- Consultez les logs de chaque job
- ✅ Vert = Tous les checks passent
- ❌ Rouge = Échec (vérifier les logs)

### 2. Sur SonarQube
- Accédez à http://localhost:9000
- Projet : **Waste2Product**
- Consultez :
  - 🐛 Bugs
  - 🔒 Vulnérabilités
  - 💩 Code Smells
  - 📊 Couverture de code
  - ⭐ Quality Gate

---

## 🚨 Dépannage

### ❌ Erreur: "Unable to connect to SonarQube"
**Solution** : Vérifiez que :
1. SonarQube est démarré : `docker-compose ps`
2. Le runner peut accéder à `http://localhost:9000`
3. `SONAR_HOST_URL` dans GitHub Secrets est correct

### ❌ Erreur: "No runner available"
**Solution** :
1. Vérifiez que le runner est actif
2. GitHub → Settings → Actions → Runners
3. Redémarrez le runner si nécessaire

### ❌ Quality Gate FAIL
**Solution** :
1. Consultez SonarQube pour voir les problèmes
2. Corrigez les bugs/vulnérabilités
3. Augmentez la couverture de tests si nécessaire
4. Re-push le code

---

## 📈 Métriques de qualité recommandées

| Métrique | Objectif | Critique |
|----------|----------|----------|
| Coverage | > 80% | > 60% |
| Bugs | 0 | < 5 |
| Vulnerabilities | 0 | < 3 |
| Code Smells | < 100 | < 300 |
| Duplications | < 3% | < 5% |

---

## 🔐 Sécurité

**Secrets GitHub à ne JAMAIS commiter** :
- ✅ `SONAR_TOKEN` → Dans GitHub Secrets
- ✅ `SONAR_HOST_URL` → Dans GitHub Secrets
- ✅ `DOCKER_USERNAME` / `DOCKER_PASSWORD` → Dans GitHub Secrets

**Fichiers à ignorer** (déjà dans .gitignore) :
- `.env`
- `coverage/`
- `.scannerwork/`

---

## ✅ Checklist avant chaque PR

- [ ] Tests locaux passent : `docker-compose exec app vendor/bin/phpunit`
- [ ] Code formatté : `docker-compose exec app vendor/bin/php-cs-fixer fix`
- [ ] Migrations OK : `docker-compose exec app php artisan migrate:fresh`
- [ ] Coverage > 80%
- [ ] SonarQube local : 0 bugs, 0 vulnérabilités

---

**🎉 Votre CI/CD est prête ! Chaque push lance automatiquement les tests et l'analyse SonarQube.**
