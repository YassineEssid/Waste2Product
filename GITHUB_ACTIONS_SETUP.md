# 🚀 Configuration GitHub Actions - Guide Étape par Étape

## 📋 Vue d'ensemble

Ce guide vous accompagne dans la configuration de l'intégration continue avec **GitHub Actions** et **SonarCloud** pour votre projet Waste2Product.

---

## ✅ Ce qui est déjà configuré

Votre projet contient déjà :
- ✅ Fichiers workflow : `.github/workflows/tests.yml` et `.github/workflows/ci-cd.yml`
- ✅ Configuration PHPUnit : `phpunit.xml`
- ✅ Tests unitaires : `tests/Unit/` et `tests/Feature/`
- ✅ Infrastructure Docker complète

---

## 🎯 Étape 1 : Créer un compte SonarCloud (5 minutes)

### 1.1 Inscription

1. Allez sur **https://sonarcloud.io**
2. Cliquez sur **"Start now"** ou **"Log in"**
3. Choisissez **"Sign up with GitHub"**
4. Autorisez SonarCloud à accéder à votre compte GitHub

### 1.2 Créer une organisation

1. Une fois connecté, cliquez sur **"+"** en haut à droite
2. Sélectionnez **"Create new organization"**
3. Choisissez votre compte GitHub
4. Donnez un nom à votre organisation (ex: `yassineessid-org`)
5. Choisissez le plan **FREE** (gratuit pour projets publics)

---

## 🔧 Étape 2 : Importer votre projet

### 2.1 Ajouter un projet

1. Dans SonarCloud, cliquez sur **"+"** → **"Analyze new project"**
2. Sélectionnez le repository **"Waste2Product"**
3. Cliquez sur **"Set Up"**

### 2.2 Choisir la méthode d'analyse

1. Sélectionnez **"With GitHub Actions"**
2. SonarCloud va vous guider avec des instructions

### 2.3 Noter les informations

SonarCloud vous donnera :
- **Organization Key** : `votre-organization-key`
- **Project Key** : `YassineEssid_Waste2Product` (généré automatiquement)

📝 **Notez ces valeurs**, vous en aurez besoin !

---

## 🔑 Étape 3 : Générer un token SonarCloud

### 3.1 Créer le token

1. Cliquez sur votre avatar (en haut à droite) → **"My Account"**
2. Allez dans l'onglet **"Security"**
3. Dans la section **"Generate Tokens"** :
   - **Name** : `github-actions-waste2product`
   - **Type** : `User Token`
   - Cliquez sur **"Generate"**

4. **⚠️ IMPORTANT** : Copiez le token immédiatement !
   - Il ressemble à : `sqp_1234567890abcdef...`
   - Vous ne pourrez plus le voir après

---

## 🔐 Étape 4 : Configurer les secrets GitHub

### 4.1 Accéder aux secrets

1. Allez sur votre repository GitHub : **https://github.com/YassineEssid/Waste2Product**
2. Cliquez sur **"Settings"** (en haut)
3. Dans le menu gauche : **"Secrets and variables"** → **"Actions"**
4. Cliquez sur **"New repository secret"**

### 4.2 Ajouter les secrets

**Secret 1 : SONAR_TOKEN**
- **Name** : `SONAR_TOKEN`
- **Secret** : Collez le token que vous avez copié à l'étape 3
- Cliquez sur **"Add secret"**

**Secret 2 : SONAR_HOST_URL**
- **Name** : `SONAR_HOST_URL`
- **Secret** : `https://sonarcloud.io`
- Cliquez sur **"Add secret"**

**Secret 3 (optionnel) : SONAR_ORGANIZATION** 
- **Name** : `SONAR_ORGANIZATION`
- **Secret** : Votre organization key de l'étape 2
- Cliquez sur **"Add secret"**

✅ Vous devriez maintenant avoir 2-3 secrets configurés !

---

## 📝 Étape 5 : Mettre à jour sonar-project.properties

### 5.1 Ouvrir le fichier

Ouvrez le fichier `sonar-project.properties` à la racine du projet.

### 5.2 Mettre à jour les valeurs

```properties
# Organisation SonarCloud (remplacez par la vôtre)
sonar.organization=votre-organization-key

# Clé du projet (généré par SonarCloud)
sonar.projectKey=YassineEssid_Waste2Product

# Nom du projet
sonar.projectName=Waste2Product

# URL SonarCloud
sonar.host.url=https://sonarcloud.io

# Source code
sonar.sources=app
sonar.tests=tests

# Exclusions
sonar.exclusions=vendor/**,storage/**,node_modules/**,bootstrap/cache/**,public/**

# Tests
sonar.test.inclusions=tests/**/*Test.php

# Coverage
sonar.php.coverage.reportPaths=coverage/clover.xml

# Encoding
sonar.sourceEncoding=UTF-8
sonar.language=php
```

**⚠️ Remplacez uniquement** :
- `sonar.organization` : par votre organization key
- `sonar.projectKey` : par votre project key (si différent)

---

## 🎬 Étape 6 : Tester le workflow

### 6.1 Commit et push

```bash
# Ajouter les modifications
git add .

# Commit
git commit -m "ci: Configure GitHub Actions and SonarCloud"

# Push vers GitHub
git push origin main
```

### 6.2 Vérifier l'exécution

1. Allez sur GitHub : **https://github.com/YassineEssid/Waste2Product**
2. Cliquez sur l'onglet **"Actions"**
3. Vous devriez voir un workflow en cours d'exécution 🟡

### 6.3 Suivre le workflow

Le workflow va :
1. ✅ Installer les dépendances PHP
2. ✅ Configurer la base de données MySQL
3. ✅ Exécuter les tests unitaires
4. ✅ Générer le rapport de couverture
5. ✅ Envoyer à SonarCloud
6. ✅ Vérifier le Quality Gate

⏱️ **Durée** : ~3-5 minutes

---

## 📊 Étape 7 : Consulter les résultats

### 7.1 Sur GitHub Actions

1. Onglet **"Actions"** de votre repository
2. Cliquez sur le workflow exécuté
3. Vous verrez :
   - ✅ Tests passés/échoués
   - 📊 Couverture de code
   - 📋 Logs détaillés

### 7.2 Sur SonarCloud

1. Allez sur **https://sonarcloud.io**
2. Cliquez sur votre projet **"Waste2Product"**
3. Vous verrez :
   - 🐛 **Bugs** : Erreurs détectées
   - 🔒 **Vulnerabilities** : Failles de sécurité
   - 💩 **Code Smells** : Mauvaises pratiques
   - 📊 **Coverage** : Couverture de tests
   - 🔄 **Duplications** : Code dupliqué
   - ⭐ **Ratings** : Notes A-E (Maintenabilité, Fiabilité, Sécurité)

---

## 🎨 Étape 8 : Ajouter les badges au README

### 8.1 Récupérer les badges SonarCloud

1. Sur SonarCloud, dans votre projet
2. Cliquez sur **"Information"** (en bas à droite)
3. Copiez le code Markdown des badges

### 8.2 Ajouter au README.md

Ajoutez ces badges en haut de votre `README.md` :

```markdown
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=YassineEssid_Waste2Product&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=YassineEssid_Waste2Product)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=YassineEssid_Waste2Product&metric=coverage)](https://sonarcloud.io/summary/new_code?id=YassineEssid_Waste2Product)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=YassineEssid_Waste2Product&metric=bugs)](https://sonarcloud.io/summary/new_code?id=YassineEssid_Waste2Product)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=YassineEssid_Waste2Product&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=YassineEssid_Waste2Product)
```

---

## 🔄 Workflow automatique

Désormais, **à chaque fois** que vous :
- ✅ Faites un `push` sur `main` ou `develop`
- ✅ Créez une Pull Request

GitHub Actions va **automatiquement** :
1. 🧪 Exécuter tous les tests
2. 📊 Générer la couverture de code
3. 🔍 Analyser la qualité avec SonarCloud
4. ✅ Vérifier le Quality Gate
5. 💬 Commenter la PR avec les résultats

---

## 🎯 Objectifs de qualité recommandés

| Métrique | Objectif | Critique |
|----------|----------|----------|
| **Coverage** | > 80% | > 60% |
| **Bugs** | 0 | < 5 |
| **Vulnerabilities** | 0 | < 3 |
| **Code Smells** | < 100 | < 300 |
| **Duplications** | < 3% | < 5% |
| **Maintainability** | A | > B |
| **Reliability** | A | > B |
| **Security** | A | > B |

---

## 🔧 Dépannage

### ❌ Erreur : "SONAR_TOKEN not found"

**Solution** : Vérifiez que vous avez bien ajouté le secret `SONAR_TOKEN` dans GitHub Settings → Secrets

### ❌ Erreur : "Project not found in SonarCloud"

**Solution** : Vérifiez que `sonar.organization` et `sonar.projectKey` correspondent à votre projet SonarCloud

### ❌ Tests échouent

**Solution** : Vérifiez les logs dans GitHub Actions, puis corrigez les tests localement

### ❌ Quality Gate échoue

**Solution** : Consultez SonarCloud pour voir quelles métriques ne passent pas, puis corrigez le code

---

## 📚 Ressources

- **GitHub Actions Docs** : https://docs.github.com/actions
- **SonarCloud Docs** : https://docs.sonarcloud.io
- **Laravel Testing** : https://laravel.com/docs/testing
- **PHPUnit** : https://phpunit.de/documentation.html

---

## ✅ Checklist finale

- [ ] Compte SonarCloud créé
- [ ] Organisation SonarCloud créée
- [ ] Projet importé dans SonarCloud
- [ ] Token SonarCloud généré
- [ ] Secrets GitHub configurés (`SONAR_TOKEN`, `SONAR_HOST_URL`)
- [ ] `sonar-project.properties` mis à jour
- [ ] Code poussé sur GitHub
- [ ] Workflow GitHub Actions exécuté avec succès
- [ ] Résultats visibles sur SonarCloud
- [ ] Badges ajoutés au README.md

---

**🎉 Félicitations ! Votre CI/CD est maintenant opérationnel !**

Chaque modification de code sera automatiquement testée et analysée. 🚀
