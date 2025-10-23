# 🧪 Guide de Test : Notification de Messages

## ⚡ Test Rapide (2 minutes)

### Prérequis
- ✅ Serveur Laravel en cours d'exécution
- ✅ 2 comptes utilisateurs créés
- ✅ Au moins 1 article dans le marketplace

---

## 🎯 Étape 1 : Vérifier l'Icône

1. **Connectez-vous** avec le Compte A
2. **Regardez la navbar** (en haut à droite)
3. **Cherchez** l'icône d'enveloppe 📧

**Attendu** :
```
Navbar : [Dashboard] [My Items] [Events] [Marketplace]     📧  👤 User A ▼
                                                           ↑
                                                    Icône visible
```

✅ **OK** : L'icône est présente  
❌ **Problème** : Icône absente → Vérifiez `app.blade.php`

---

## 🎯 Étape 2 : Badge Initial

1. **Vérifiez le badge** à côté de l'icône 📧

**Attendu** :
- Si **aucun message non lu** : Badge **masqué** (rien n'apparaît)
- Si **messages non lus existants** : Badge **rouge** avec chiffre

✅ **OK** : Comportement correct  
❌ **Problème** : Badge toujours visible → Vérifiez la logique JavaScript

---

## 🎯 Étape 3 : Envoyer un Message

### Sur le Compte A (Acheteur)

1. **Allez sur** `/marketplace`
2. **Cliquez** sur un article
3. **Cliquez** sur le bouton **"Contact Seller"**
4. **Tapez** : "Hello, is this still available?"
5. **Cliquez** "Send"

**Attendu** :
- Message apparaît en **bleu** à droite
- Message envoyé avec succès

---

## 🎯 Étape 4 : Vérifier la Notification

### Sur le Compte B (Vendeur)

1. **Ouvrez un navigateur différent** (ou mode incognito)
2. **Connectez-vous** avec le Compte B (vendeur)
3. **Attendez 3-5 secondes** (temps du polling)
4. **Regardez la navbar**

**Attendu** :
```
📧 🔴1
   ↑
Badge rouge
apparaît !
```

**Comportement du Badge** :
- 🔴 Badge **rouge** visible
- Affiche **"1"**
- **Animation pulse** (grossit/rétrécit légèrement)
- **Ombre rouge** autour du badge

✅ **OK** : Badge apparaît avec "1"  
❌ **Problème** : Badge n'apparaît pas → Voir Troubleshooting

---

## 🎯 Étape 5 : Animation sur Nouveau Message

### Sur le Compte A (Renvoie un message)

1. **Dans la conversation ouverte**, tapez : "When can I pick it up?"
2. **Cliquez** "Send"

### Sur le Compte B (Observe)

1. **NE CLIQUEZ PAS** sur l'icône
2. **Attendez 3-5 secondes**
3. **Observez le badge**

**Attendu** :
- Badge passe de **🔴1** à **🔴2**
- **Animation bounce** (rebond rapide)
- Son de notification (si activé)

✅ **OK** : Animation visible  
❌ **Problème** : Pas d'animation → Vérifiez CSS

---

## 🎯 Étape 6 : Lecture de Message

### Sur le Compte B

1. **Cliquez** sur l'icône 📧
2. **Liste des conversations** s'affiche
3. **Cliquez** sur la conversation avec Compte A
4. **Messages apparaissent** (2 messages en gris)
5. **Attendez 3-5 secondes**
6. **Regardez la navbar**

**Attendu** :
- Badge **disparaît** (display: none)
- Icône 📧 reste visible

✅ **OK** : Badge masqué après lecture  
❌ **Problème** : Badge reste → Vérifiez `MessageController@show`

---

## 🎯 Étape 7 : Hover Effect

1. **Passez la souris** sur l'icône 📧
2. **Ne cliquez pas**, juste survolez

**Attendu** :
- Icône **grossit légèrement** (scale 1.1)
- Transition **fluide** (0.3s)

✅ **OK** : Effet de survol visible  
❌ **Problème** : Aucun effet → Vérifiez CSS `#messagesIcon:hover`

---

## 🎯 Étape 8 : Compteur 99+

### Test Avancé (Optionnel)

1. **Connectez-vous** avec un compte ayant **plus de 99 messages non lus**
2. **Regardez le badge**

**Attendu** :
- Badge affiche **"99+"** au lieu d'un nombre exact

---

## 📊 Résumé des Tests

| Test | Description | Attendu | Résultat |
|------|-------------|---------|----------|
| 1 | Icône présente | 📧 visible | ☐ |
| 2 | Badge initial | Masqué si 0 messages | ☐ |
| 3 | Envoyer message | Message bleu à droite | ☐ |
| 4 | Notification | 🔴1 apparaît en 3-5 sec | ☐ |
| 5 | Animation | Bounce lors augmentation | ☐ |
| 6 | Lecture | Badge disparaît | ☐ |
| 7 | Hover | Icône grossit au survol | ☐ |
| 8 | 99+ | Badge affiche "99+" si > 99 | ☐ |

---

## 🐛 Troubleshooting Rapide

### ❌ Badge n'apparaît jamais

**Console du navigateur (F12)** :
```javascript
// Vérifier les erreurs JavaScript
// Onglet Console → Chercher erreurs en rouge
```

**Network (F12 → Network)** :
```
Chercher : /messages/unread/count
Status : Doit être 200
Response : {"unread_count": 1}
```

**Vérifications** :
```bash
# Route existe ?
php artisan route:list | grep unread

# Logs Laravel
tail -f storage/logs/laravel.log
```

---

### ❌ Badge reste bloqué à un chiffre

**Causes possibles** :
1. Messages pas marqués comme lus
2. Polling bloqué

**Solutions** :
```sql
-- Marquer tous les messages comme lus manuellement
UPDATE messages SET is_read = 1 WHERE conversation_id = 1;
```

---

### ❌ Animation ne fonctionne pas

**Vérifier CSS** :
```javascript
// Console (F12)
const badge = document.getElementById('messagesBadge');
console.log(badge.style.animation); // Doit afficher "pulse 2s infinite"
```

---

### ❌ Polling ne démarre pas

**Vérifier** :
```javascript
// Console (F12)
console.log('Auth:', '{{ auth()->check() }}'); // Doit être "1" ou "true"
```

Le script est dans un bloc `@auth`, donc ne s'exécute que si connecté.

---

## ✅ Test de Performance

### Charge du Polling

1. **Ouvrez** F12 → Network
2. **Attendez 30 secondes**
3. **Comptez** les requêtes `/messages/unread/count`

**Attendu** :
- **10 requêtes** en 30 secondes
- 1 requête toutes les **3 secondes**
- Payload : **~30 bytes**

---

## 🎉 Validation Finale

Si tous les tests passent :

✅ Icône visible  
✅ Badge apparaît/disparaît correctement  
✅ Compteur précis  
✅ Animations fluides  
✅ Polling régulier (3 sec)  
✅ Performance optimale  

**🚀 Fonctionnalité validée ! 🚀**

---

## 📸 Captures d'Écran Attendues

### Vue 1 : Aucun Message
```
Navbar: 📧 (pas de badge)
```

### Vue 2 : 1 Message Non Lu
```
Navbar: 📧 🔴1 (badge rouge animé)
```

### Vue 3 : Multiple Messages
```
Navbar: 📧 🔴5 (badge rouge pulsant)
```

### Vue 4 : Beaucoup de Messages
```
Navbar: 📧 🔴99+ (badge avec "99+")
```

---

**Temps estimé pour tous les tests : 5-10 minutes**

**🧪 Bon test ! 🧪**
