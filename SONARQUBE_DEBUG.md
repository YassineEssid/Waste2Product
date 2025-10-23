# 🔍 Diagnostic SonarQube - Pourquoi SonarQube est vide?

## ✅ Vérifications à faire

### 1. Vérifier que SonarQube tourne
```bash
docker ps | grep sonarqube
# Devrait afficher: sonarqube - Up X hours
```

### 2. Accéder à SonarQube
Ouvrez votre navigateur: **http://localhost:9000**
- Login par défaut: `admin`
- Password par défaut: `admin` (changez-le au premier login)

### 3. Vérifier le projet existe
Dans SonarQube UI:
- Allez dans **Projects**
- Cherchez `waste2product`
- Si absent → L'analyse ne s'est jamais exécutée

### 4. Vérifier les secrets GitHub
Allez sur: https://github.com/YassineEssid/Waste2Product/settings/secrets/actions

Vérifiez que vous avez:
- ✅ `SONAR_TOKEN` → Token SonarQube
- ✅ `SONAR_HOST_URL` → `http://localhost:9000` OU l'IP de votre machine

**⚠️ PROBLÈME FRÉQUENT**: `localhost` dans GitHub Actions ne marche PAS!

### 5. Corriger SONAR_HOST_URL
Le self-hosted runner doit accéder à SonarQube.

**Solution A - IP locale:**
```bash
# Trouvez votre IP locale
ipconfig
# Utilisez l'IP comme: http://192.168.X.X:9000
```

**Solution B - Réseau Docker:**
Si le runner tourne dans Docker:
```bash
SONAR_HOST_URL=http://host.docker.internal:9000
```

**Solution C - Même réseau Docker:**
```yaml
SONAR_HOST_URL=http://sonarqube:9000
```

### 6. Créer un token SonarQube
1. Connectez-vous à http://localhost:9000
2. Allez dans **My Account** → **Security** → **Generate Tokens**
3. Nom: `GitHub Actions`
4. Type: `Global Analysis Token`
5. Copiez le token
6. Ajoutez-le dans GitHub Secrets comme `SONAR_TOKEN`

### 7. Tester l'analyse localement
```bash
# Depuis la racine du projet
docker run --rm \
  --network waste2product_waste2product \
  -e SONAR_HOST_URL=http://sonarqube:9000 \
  -e SONAR_TOKEN=votre_token_ici \
  -v ${PWD}:/usr/src \
  sonarsource/sonar-scanner-cli
```

### 8. Vérifier les logs du runner
Si votre self-hosted runner tourne:
```bash
# Logs du runner GitHub
cd ~/actions-runner
./logs/Runner_*.log
```

### 9. Workflow alternatif - Sans self-hosted
Si le self-hosted pose problème, utilisez **GitHub-hosted + ngrok**:

```yaml
sonarqube:
  runs-on: ubuntu-latest
  needs: tests
  
  steps:
    - name: Setup ngrok
      run: |
        wget https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz
        tar xvzf ngrok-v3-stable-linux-amd64.tgz
        ./ngrok http 9000 &
        
    - name: SonarQube Scan
      uses: SonarSource/sonarqube-scan-action@master
      env:
        SONAR_TOKEN: ${{ secrets.SONAR_TOKEN }}
        SONAR_HOST_URL: http://votre-tunnel-ngrok.ngrok.io
```

## 🎯 Solution Rapide

**Étape 1:** Vérifiez votre `SONAR_HOST_URL`
```bash
# Si runner local
SONAR_HOST_URL=http://192.168.X.X:9000

# Si runner dans Docker
SONAR_HOST_URL=http://host.docker.internal:9000
```

**Étape 2:** Re-déclenchez le workflow
```bash
git commit --allow-empty -m "trigger: Test SonarQube analysis"
git push
```

**Étape 3:** Surveillez les logs
- Dans GitHub Actions → Job `sonarqube` → Regardez les erreurs
- Dans SonarQube → **Administration** → **System** → **System Info**

## 🚨 Erreurs Courantes

### Erreur: "Connection refused"
→ `SONAR_HOST_URL` incorrect. Utilisez l'IP au lieu de localhost.

### Erreur: "Unauthorized"
→ `SONAR_TOKEN` invalide. Régénérez un nouveau token.

### Erreur: "Quality Gate timeout"
→ Augmentez le timeout ou désactivez temporairement Quality Gate.

### Pas d'erreur mais projet vide
→ L'analyse n'a jamais démarré. Vérifiez les logs du job.

## 📊 Résultat Attendu

Après une analyse réussie, vous devriez voir:
- **Projects** → `waste2product` 
- **Lines of Code**: ~3273
- **Coverage**: ~11%
- **Issues**: Liste des bugs/code smells détectés
