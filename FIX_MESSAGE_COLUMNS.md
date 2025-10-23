# Fix: Column not found - user_id, body, read_at

## Nouveau Problème Après Fix Précédent
Après avoir créé les tables `conversations` et `messages`, une nouvelle erreur est apparue :

```
SQLSTATE[42S22]: Column not found: 1054 
Champ 'user_id' inconnu dans where clause
```

**Fichier**: `app/Http/Controllers/MessageController.php` ligne 35

## Cause Racine
Le `MessageController` et la vue `messages/show.blade.php` utilisaient les **anciens noms de colonnes** qui ne correspondent pas à la structure de la table `messages` que nous venons de créer.

### Incohérence des Noms de Colonnes

| Ancien (Code) | Nouveau (Table) | Type |
|---------------|-----------------|------|
| `user_id` | `sender_id` | User qui envoie |
| `body` | `message` | Contenu du message |
| `read_at` | `is_read` | Statut de lecture |
| `user` (relation) | `sender` (relation) | Relation vers User |

## Solutions Appliquées

### 1. Mise à Jour du Contrôleur `MessageController.php`

#### Méthode `show()` - Lignes 28-41

**Avant**:
```php
public function show(Conversation $conversation)
{
    $this->authorize('view', $conversation);

    $conversation->load(['messages.user', 'item.images']);

    // Mark messages as read
    $conversation->messages()->where('user_id', '!=', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);

    return view('messages.show', compact('conversation'));
}
```

**Après**:
```php
public function show(Conversation $conversation)
{
    $this->authorize('view', $conversation);

    $conversation->load(['messages.sender', 'item.images']);

    // Mark messages as read (only messages from the other person)
    $conversation->messages()
        ->where('sender_id', '!=', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return view('messages.show', compact('conversation'));
}
```

**Changements**:
- ✅ `messages.user` → `messages.sender` (eager loading de la relation)
- ✅ `user_id` → `sender_id` (nom de colonne)
- ✅ `whereNull('read_at')` → `where('is_read', false)` (boolean au lieu de timestamp)
- ✅ `['read_at' => now()]` → `['is_read' => true]` (boolean)

#### Méthode `store()` - Lignes 43-61

**Avant**:
```php
public function store(Request $request, Conversation $conversation)
{
    $this->authorize('update', $conversation);

    $request->validate([
        'body' => 'required|string',
    ]);

    $message = $conversation->messages()->create([
        'user_id' => Auth::id(),
        'body' => $request->body,
    ]);

    // Broadcast the message
    event(new \App\Events\NewMessage($message));

    return back();
}
```

**Après**:
```php
public function store(Request $request, Conversation $conversation)
{
    $this->authorize('update', $conversation);

    $request->validate([
        'message' => 'required|string',
    ]);

    $message = $conversation->messages()->create([
        'sender_id' => Auth::id(),
        'message' => $request->message,
        'is_read' => false,
    ]);

    // Update conversation's last_message_at
    $conversation->update(['last_message_at' => now()]);

    // Broadcast the message (if you have broadcasting setup)
    // event(new \App\Events\NewMessage($message));

    return back()->with('success', 'Message sent successfully!');
}
```

**Changements**:
- ✅ Validation: `body` → `message`
- ✅ Création: `user_id` → `sender_id`
- ✅ Création: `body` → `message`
- ✅ Ajout de `is_read => false` par défaut
- ✅ Mise à jour de `last_message_at` dans la conversation
- ✅ Message de succès ajouté

### 2. Mise à Jour de la Vue `messages/show.blade.php`

**Avant**:
```blade
@foreach ($conversation->messages as $message)
    <div class="d-flex flex-column {{ $message->user_id == auth()->id() ? 'align-items-end' : 'align-items-start' }}">
        <div class="p-2 rounded mb-2" style="background-color: #f1f1f1;">
            <p class="mb-0">{{ $message->body }}</p>
            <small class="text-muted">
                {{ $message->created_at->format('h:i A') }} | {{ $message->user->name }}
            </small>
        </div>
    </div>
@endforeach

<!-- Formulaire -->
<input type="text" name="body" class="form-control" placeholder="Type your message...">
```

**Après**:
```blade
@foreach ($conversation->messages as $message)
    <div class="d-flex flex-column {{ $message->sender_id == auth()->id() ? 'align-items-end' : 'align-items-start' }}">
        <div class="p-2 rounded mb-2" style="background-color: {{ $message->sender_id == auth()->id() ? '#007bff' : '#f1f1f1' }}; color: {{ $message->sender_id == auth()->id() ? '#fff' : '#000' }};">
            <p class="mb-0">{{ $message->message }}</p>
            <small class="{{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                {{ $message->created_at->format('h:i A') }} | {{ $message->sender->name }}
            </small>
        </div>
    </div>
@endforeach

<!-- Formulaire -->
<input type="text" name="message" class="form-control" placeholder="Type your message..." required>
```

**Changements**:
- ✅ `$message->user_id` → `$message->sender_id`
- ✅ `$message->body` → `$message->message`
- ✅ `$message->user->name` → `$message->sender->name`
- ✅ `name="body"` → `name="message"` dans le formulaire
- ✅ Ajout de couleurs différentes pour les messages envoyés (bleu) vs reçus (gris)
- ✅ Ajout de l'attribut `required` au champ de saisie
- ✅ Changement du délai d'auto-refresh de 5s à 10s

### 3. Suppression du Code Broadcasting Non Fonctionnel

Le code JavaScript pour Echo (Laravel Broadcasting) a été supprimé car :
- ❌ Echo n'est pas configuré dans le projet
- ❌ Axios n'est pas inclus
- ❌ L'event `NewMessage` n'existe pas

À la place, nous utilisons un **auto-refresh** simple toutes les 10 secondes.

## Amélioration de l'Interface Utilisateur

### Messages Envoyés vs Reçus

**Messages de l'utilisateur connecté** (envoyés) :
- Couleur de fond: Bleu (`#007bff`)
- Texte: Blanc (`#fff`)
- Alignement: Droite
- Timestamp: Blanc transparent

**Messages de l'autre personne** (reçus) :
- Couleur de fond: Gris clair (`#f1f1f1`)
- Texte: Noir (`#000`)
- Alignement: Gauche
- Timestamp: Gris foncé

## Structure Finale des Tables

### Table `messages`
```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_conversation_time (conversation_id, created_at)
);
```

### Modèle `Message.php`
```php
class Message extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'message', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
```

## Test du Système

### Scénario Complet :

1. **Connexion** utilisateur A (acheteur)
2. **Aller sur** marketplace item d'utilisateur B
3. **Cliquer** "Contact Seller"
4. ✅ **Vérifier**: Redirection vers `/messages/1`
5. ✅ **Vérifier**: Page de conversation s'affiche correctement
6. **Taper** un message: "Hello, is this still available?"
7. **Cliquer** "Send"
8. ✅ **Vérifier**: Message apparaît en bleu à droite
9. **Connexion** utilisateur B (vendeur)
10. **Aller sur** `/messages`
11. **Cliquer** sur la conversation
12. ✅ **Vérifier**: Message de A apparaît en gris à gauche
13. **Répondre**: "Yes, it is!"
14. ✅ **Vérifier**: Message de B apparaît en bleu à droite
15. **Retour** utilisateur A
16. ✅ **Vérifier**: Message de B apparaît en gris à gauche

### Vérification en Base de Données

```sql
-- Voir tous les messages d'une conversation
SELECT 
    m.id,
    sender.name as sender_name,
    m.message,
    m.is_read,
    m.created_at
FROM messages m
JOIN users sender ON m.sender_id = sender.id
WHERE m.conversation_id = 1
ORDER BY m.created_at ASC;

-- Vérifier le statut de lecture
SELECT 
    COUNT(*) as total_messages,
    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read_messages,
    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages
FROM messages
WHERE conversation_id = 1;

-- Voir la dernière activité de la conversation
SELECT 
    c.id,
    c.last_message_at,
    buyer.name as buyer_name,
    seller.name as seller_name,
    item.title as item_title,
    (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id) as message_count
FROM conversations c
JOIN users buyer ON c.buyer_id = buyer.id
JOIN users seller ON c.seller_id = seller.id
JOIN marketplace_items item ON c.marketplace_item_id = item.id
WHERE c.id = 1;
```

## Fichiers Modifiés

1. ✅ `app/Http/Controllers/MessageController.php`
   - Méthode `show()` - Lignes 28-41
   - Méthode `store()` - Lignes 43-61

2. ✅ `resources/views/messages/show.blade.php`
   - Toute la vue réécrite pour utiliser les nouveaux noms de colonnes
   - Interface améliorée avec couleurs distinctes
   - Suppression du code Broadcasting non fonctionnel

## Améliorations Futures Possibles

### 1. Broadcasting en Temps Réel
```php
// Installer Laravel Echo et Pusher
composer require pusher/pusher-php-server
npm install --save-dev laravel-echo pusher-js

// Configurer dans .env
BROADCAST_DRIVER=pusher
```

### 2. Pagination des Messages
```php
// Dans MessageController@show
$messages = $conversation->messages()
    ->orderBy('created_at', 'desc')
    ->paginate(50);
```

### 3. Notifications de Nouveaux Messages
```php
// Créer une notification
php artisan make:notification NewMessageNotification
```

### 4. Recherche dans les Messages
```php
// Ajouter un champ de recherche
$messages = $conversation->messages()
    ->where('message', 'LIKE', "%{$search}%")
    ->get();
```

## Statut
✅ **CORRIGÉ** - Tous les noms de colonnes alignés entre code et base de données

---
*Fix appliqué le: 2025-10-21*
*Problème: Noms de colonnes incohérents entre code (user_id, body, read_at) et table (sender_id, message, is_read)*
*Solution: Mis à jour le contrôleur et la vue pour utiliser les bons noms de colonnes*
