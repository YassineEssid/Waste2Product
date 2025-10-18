# Event Comments - Documentation

## 📋 Vue d'ensemble

Le module **Event Comments** est une nouvelle entité qui permet aux utilisateurs de commenter et d'évaluer les événements communautaires. Cette fonctionnalité enrichit l'expérience utilisateur en permettant le partage de retours d'expérience et d'opinions sur les événements.

## 🎯 Fonctionnalités

### 1. **CRUD Complet**
- ✅ **Create** : Ajouter un commentaire sur un événement
- ✅ **Read** : Afficher tous les commentaires ou un commentaire spécifique
- ✅ **Update** : Modifier son propre commentaire
- ✅ **Delete** : Supprimer son propre commentaire

### 2. **Système de notation**
- Évaluation par étoiles (1 à 5 étoiles)
- Notation optionnelle
- Affichage visuel avec des icônes d'étoiles
- Calcul de la note moyenne

### 3. **Système d'approbation**
- Les administrateurs peuvent approuver/désapprouver les commentaires
- Filtrage par statut d'approbation
- Badge visuel pour les statuts

### 4. **Filtres et recherche avancés**
- Recherche par texte
- Filtrage par événement
- Filtrage par note
- Filtrage par statut d'approbation
- Tri multiple (plus récent, plus ancien, note haute/basse)

### 5. **Design moderne et responsive**
- Interface utilisateur élégante avec Bootstrap 5
- Cards avec animations au survol
- Gradient colors pour les headers
- Icons Font Awesome
- Responsive sur tous les écrans

## 📁 Structure des fichiers

```
app/
├── Models/
│   └── EventComment.php           # Modèle principal
├── Http/
│   └── Controllers/
│       └── EventCommentController.php   # Contrôleur CRUD
database/
├── migrations/
│   └── 2025_10_18_121634_create_event_comments_table.php
└── seeders/
    └── EventCommentsSeeder.php
resources/
└── views/
    └── event-comments/
        ├── index.blade.php        # Liste des commentaires
        ├── create.blade.php       # Formulaire de création
        ├── edit.blade.php         # Formulaire d'édition
        └── show.blade.php         # Détails d'un commentaire
routes/
└── web.php                        # Routes configurées
```

## 🗄️ Schéma de la base de données

### Table: `event_comments`

| Colonne               | Type          | Description                          |
|-----------------------|---------------|--------------------------------------|
| id                    | bigInteger    | Clé primaire                        |
| community_event_id    | foreignId     | Référence à l'événement             |
| user_id               | foreignId     | Référence à l'utilisateur           |
| comment               | text          | Contenu du commentaire              |
| rating                | integer       | Note de 1 à 5 (nullable)            |
| is_approved           | boolean       | Statut d'approbation                |
| commented_at          | timestamp     | Date du commentaire                 |
| created_at            | timestamp     | Date de création                    |
| updated_at            | timestamp     | Date de mise à jour                 |

### Relations

1. **EventComment** `belongsTo` **CommunityEvent**
2. **EventComment** `belongsTo` **User**
3. **CommunityEvent** `hasMany` **EventComment**

## 🔗 Routes disponibles

| Méthode | URI                                      | Action                | Nom de la route                    |
|---------|------------------------------------------|-----------------------|-----------------------------------|
| GET     | /event-comments                          | index                 | event-comments.index              |
| GET     | /event-comments/create                   | create                | event-comments.create             |
| POST    | /event-comments                          | store                 | event-comments.store              |
| GET     | /event-comments/{eventComment}           | show                  | event-comments.show               |
| GET     | /event-comments/{eventComment}/edit      | edit                  | event-comments.edit               |
| PUT     | /event-comments/{eventComment}           | update                | event-comments.update             |
| DELETE  | /event-comments/{eventComment}           | destroy               | event-comments.destroy            |
| POST    | /event-comments/{eventComment}/toggle-approval | toggleApproval   | event-comments.toggle-approval    |

## 🎨 Captures d'écran des fonctionnalités

### Page d'index
- Hero section avec statistiques
- Filtres avancés (événement, note, statut, recherche)
- Cards de commentaires avec animations
- Pagination

### Page de création
- Formulaire élégant avec sélection d'événement
- Système de notation visuel avec des étoiles
- Zone de texte pour le commentaire
- Conseils pour rédiger un bon commentaire

### Page de détails
- Affichage complet du commentaire
- Informations sur l'événement associé
- Note avec étoiles
- Actions (éditer, supprimer, approuver)
- Commentaires similaires

### Page d'édition
- Modification du commentaire et de la note
- Option d'approbation pour les admins
- Informations sur le commentaire actuel

## 🔐 Permissions

### Utilisateurs authentifiés
- ✅ Voir tous les commentaires approuvés
- ✅ Créer un nouveau commentaire
- ✅ Modifier leurs propres commentaires
- ✅ Supprimer leurs propres commentaires

### Administrateurs
- ✅ Toutes les permissions utilisateur
- ✅ Modifier n'importe quel commentaire
- ✅ Supprimer n'importe quel commentaire
- ✅ Approuver/Désapprouver les commentaires

## 💡 Utilisation

### Créer un commentaire

```php
use App\Models\EventComment;

EventComment::create([
    'community_event_id' => 1,
    'user_id' => auth()->id(),
    'comment' => 'Excellent événement !',
    'rating' => 5,
    'is_approved' => true,
    'commented_at' => now(),
]);
```

### Récupérer les commentaires d'un événement

```php
$event = CommunityEvent::find(1);
$comments = $event->comments()->approved()->get();
```

### Scopes disponibles

```php
// Commentaires approuvés uniquement
EventComment::approved()->get();

// Commentaires pour un événement spécifique
EventComment::forEvent($eventId)->get();

// Commentaires d'un utilisateur
EventComment::byUser($userId)->get();

// Commentaires avec une note spécifique
EventComment::withRating(5)->get();
```

## 🚀 Installation et configuration

1. **Exécuter la migration**
```bash
php artisan migrate
```

2. **Seed les données de test** (optionnel)
```bash
php artisan db:seed --class=EventCommentsSeeder
```

3. **Accéder à la fonctionnalité**
- URL : `http://localhost:8000/event-comments`
- Menu : Sidebar > Community > Event Comments

## ✨ Améliorations futures possibles

- [ ] Notifications par email lors de nouveaux commentaires
- [ ] Réponses aux commentaires (système de threading)
- [ ] Likes/Dislikes sur les commentaires
- [ ] Signalement de commentaires inappropriés
- [ ] Export des commentaires en CSV/PDF
- [ ] Statistiques détaillées par événement
- [ ] Intégration d'images dans les commentaires
- [ ] Modération automatique avec IA

## 🐛 Dépannage

### Les commentaires ne s'affichent pas
- Vérifiez que la migration a été exécutée
- Assurez-vous d'être authentifié
- Vérifiez que des commentaires existent dans la base

### Erreur 403 lors de la modification
- Seul l'auteur du commentaire ou un admin peut le modifier
- Vérifiez que vous êtes bien l'auteur

### Problème avec le système de notation
- La note est optionnelle
- Les valeurs acceptées sont de 1 à 5

## 📞 Support

Pour toute question ou problème, contactez l'équipe de développement.

---

**Créé avec ❤️ pour Waste2Product**
**Version: 1.0.0**
**Date: 18 Octobre 2025**
