# ⚡ Démarrage Rapide - 3 Étapes

## 1️⃣ Configuration SonarCloud (5 minutes)

### Créer un compte
1. Allez sur https://sonarcloud.io
2. Cliquez sur **"Sign up with GitHub"**
3. Créez une organisation
4. Importez le projet **Waste2Product**

### Générer un token
1. Cliquez sur votre avatar → **My Account** → **Security**
2. Générez un token : `github-actions-waste2product`
3. **Copiez le token** (vous ne pourrez plus le voir !)

### Noter les informations
- **Organization Key** : (ex: `yassineessid-org`)
- **Project Key** : `YassineEssid_Waste2Product`
- **Token** : `sqp_...`

---

## 2️⃣ Configuration GitHub (2 minutes)

### Ajouter les secrets
1. GitHub → **Settings** → **Secrets and variables** → **Actions**
2. Ajoutez ces 2 secrets :

**SONAR_TOKEN**
```
sqp_votre_token_ici
```

**SONAR_HOST_URL**
```
https://sonarcloud.io
```

---

## 3️⃣ Mettre à jour et Push (1 minute)

### Modifier sonar-project.properties
Ouvrez `sonar-project.properties` et changez :
```properties
sonar.organization=VOTRE_ORGANIZATION_KEY
```

### Commit et Push
```bash
git add .
git commit -m "ci: Configure GitHub Actions CI/CD"
git push origin main
```

---

## ✅ C'est Fini !

Allez dans l'onglet **Actions** de GitHub pour voir le workflow s'exécuter ! 🚀

Le workflow va :
1. ✅ Exécuter les tests
2. ✅ Générer la couverture
3. ✅ Analyser avec SonarCloud
4. ✅ Vérifier le Quality Gate

**Résultats visibles sur** :
- GitHub Actions : https://github.com/YassineEssid/Waste2Product/actions
- SonarCloud : https://sonarcloud.io

---

📖 **Guide détaillé** : Voir [GITHUB_ACTIONS_SETUP.md](GITHUB_ACTIONS_SETUP.md)
