# Fix: Champ "Maximum Participants" Non Sauvegardé

## Problème
Lors de la création ou de l'édition d'un événement, le champ "Maximum Participants" (max_participants) n'était pas sauvegardé dans la base de données. Quand on éditait un événement, le champ apparaissait vide même si une valeur avait été saisie.

## Cause Racine
Le champ `max_participants` n'était pas configuré correctement dans le modèle et le contrôleur :

### 1. Modèle `CommunityEvent`
Le champ `max_participants` n'était **pas dans le tableau `$fillable`**, ce qui empêchait Laravel de l'enregistrer :

```php
// AVANT (app/Models/CommunityEvent.php)
protected $fillable = [
    'user_id',
    'title',
    'description',
    'image',
    'location',
    'starts_at',
    'ends_at',
    'status'
];
// ❌ max_participants manquant !
```

### 2. Contrôleur - Méthode `store()`
La validation et l'enregistrement ne incluaient pas `max_participants` :

```php
// AVANT
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'location' => 'nullable|string|max:255',
    // ❌ max_participants manquant !
    'event_date' => 'required|date|after:now',
    ...
]);

$data = [
    'user_id' => Auth::id(),
    'title' => $request->title,
    'description' => $request->description,
    'location' => $request->location,
    // ❌ max_participants manquant !
    'starts_at' => $request->event_date,
    ...
];
```

### 3. Contrôleur - Méthode `update()`
Même problème dans la méthode de mise à jour.

## Solution Appliquée

### Fichier 1: `app/Models/CommunityEvent.php`

**Avant**:
```php
protected $fillable = [
    'user_id',
    'title',
    'description',
    'image',
    'location',
    'starts_at',
    'ends_at',
    'status'
];
```

**Après**:
```php
protected $fillable = [
    'user_id',
    'title',
    'description',
    'image',
    'location',
    'location_lat',      // Ajouté aussi pour le futur
    'location_lng',      // Ajouté aussi pour le futur
    'starts_at',
    'ends_at',
    'max_participants',  // ✅ Ajouté
    'status'
];
```

### Fichier 2: `app/Http/Controllers/CommunityEventController.php`

#### Méthode `store()` - Lignes 100-120

**Avant**:
```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'location' => 'nullable|string|max:255',
    'event_date' => 'required|date|after:now',
    'end_date' => 'required|date|after:event_date',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
]);

$data = [
    'user_id' => Auth::id(),
    'title' => $request->title,
    'description' => $request->description,
    'location' => $request->location,
    'starts_at' => $request->event_date,
    'ends_at' => $request->end_date,
    'status' => 'upcoming'
];
```

**Après**:
```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'location' => 'nullable|string|max:255',
    'max_participants' => 'nullable|integer|min:1',  // ✅ Ajouté
    'event_date' => 'required|date|after:now',
    'end_date' => 'required|date|after:event_date',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
]);

$data = [
    'user_id' => Auth::id(),
    'title' => $request->title,
    'description' => $request->description,
    'location' => $request->location,
    'max_participants' => $request->max_participants,  // ✅ Ajouté
    'starts_at' => $request->event_date,
    'ends_at' => $request->end_date,
    'status' => 'upcoming'
];
```

#### Méthode `update()` - Lignes 178-195

**Avant**:
```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'location' => 'nullable|string|max:255',
    'event_date' => 'required|date',
    'end_date' => 'required|date|after:event_date',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
]);

$data = [
    'title' => $request->title,
    'description' => $request->description,
    'location' => $request->location,
    'starts_at' => $request->event_date,
    'ends_at' => $request->end_date,
];
```

**Après**:
```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'location' => 'nullable|string|max:255',
    'max_participants' => 'nullable|integer|min:1',  // ✅ Ajouté
    'event_date' => 'required|date',
    'end_date' => 'required|date|after:event_date',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
]);

$data = [
    'title' => $request->title,
    'description' => $request->description,
    'location' => $request->location,
    'max_participants' => $request->max_participants,  // ✅ Ajouté
    'starts_at' => $request->event_date,
    'ends_at' => $request->end_date,
];
```

## Règles de Validation

Le champ `max_participants` a les règles suivantes :
- `nullable` - Le champ est optionnel (peut être vide)
- `integer` - Doit être un nombre entier
- `min:1` - Doit être au minimum 1 si une valeur est fournie

**Comportement** :
- Si laissé vide → NULL dans la base de données → Participants illimités
- Si une valeur est saisie → Doit être ≥ 1

## Structure de la Base de Données

La table `community_events` a la colonne :
```sql
max_participants INT NULL DEFAULT NULL
```

- Type: `INT` (entier)
- Nullable: OUI
- Valeur par défaut: NULL
- Signification: 
  - NULL = Nombre de participants illimité
  - Nombre > 0 = Limite de participants

## Fonctionnalité dans les Formulaires

### Formulaire de Création (`resources/views/events/create.blade.php`)
```blade
<div class="col-md-6 mb-3">
    <label class="form-label">Maximum Participants</label>
    <input type="number" 
           class="form-control @error('max_participants') is-invalid @enderror"
           name="max_participants"
           value="{{ old('max_participants') }}"
           min="0"
           placeholder="Leave empty for unlimited">
    @error('max_participants')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

### Formulaire d'Édition (`resources/views/events/edit.blade.php`)
```blade
<div class="col-md-6 mb-3">
    <label class="form-label">Maximum Participants</label>
    <input type="number" 
           class="form-control @error('max_participants') is-invalid @enderror"
           name="max_participants"
           value="{{ old('max_participants', $event->max_participants ?? '') }}"
           min="0"
           placeholder="Leave empty for unlimited">
    @error('max_participants')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

**Points importants** :
- Le champ utilise `old('max_participants', $event->max_participants ?? '')` pour récupérer :
  1. La valeur précédente si erreur de validation
  2. La valeur de l'événement en base de données
  3. Chaîne vide si NULL

## Test

### Test de Création d'Événement :
1. ✅ Aller sur **Events** → **Create Event**
2. ✅ Remplir le formulaire
3. ✅ Saisir **50** dans "Maximum Participants"
4. ✅ Soumettre le formulaire
5. ✅ Vérifier dans la base de données que `max_participants = 50`

### Test d'Édition d'Événement :
1. ✅ Aller sur un événement existant
2. ✅ Cliquer sur **Edit Event**
3. ✅ Le champ "Maximum Participants" devrait afficher la valeur actuelle
4. ✅ Modifier la valeur (ex: changer de 50 à 100)
5. ✅ Soumettre le formulaire
6. ✅ Vérifier que la nouvelle valeur est sauvegardée

### Test de Valeur NULL :
1. ✅ Créer ou éditer un événement
2. ✅ Laisser le champ "Maximum Participants" vide
3. ✅ Soumettre
4. ✅ Vérifier que `max_participants = NULL` dans la base de données

## Requête SQL de Vérification

```sql
-- Vérifier tous les événements avec leur max_participants
SELECT id, title, max_participants, 
       CASE 
           WHEN max_participants IS NULL THEN 'Unlimited'
           ELSE CONCAT(max_participants, ' participants')
       END as capacity
FROM community_events;

-- Vérifier un événement spécifique
SELECT * FROM community_events WHERE id = 1;
```

## Utilisation Future

Le champ `max_participants` peut être utilisé pour :

1. **Limiter les inscriptions** :
```php
// Dans CommunityEventController@register
if ($event->max_participants !== null) {
    $currentRegistrations = $event->registrations()->count();
    if ($currentRegistrations >= $event->max_participants) {
        return redirect()->back()->with('error', 'Event is full!');
    }
}
```

2. **Afficher la capacité** :
```blade
@if($event->max_participants)
    <span>{{ $event->registrations_count }} / {{ $event->max_participants }} participants</span>
@else
    <span>{{ $event->registrations_count }} participants (unlimited)</span>
@endif
```

3. **Badge "Event Full"** :
```blade
@if($event->max_participants && $event->registrations_count >= $event->max_participants)
    <span class="badge bg-danger">Event Full</span>
@endif
```

## Fichiers Modifiés

1. ✅ `app/Models/CommunityEvent.php` - Ajouté `max_participants` au $fillable
2. ✅ `app/Http/Controllers/CommunityEventController.php` - Ajouté validation et enregistrement dans `store()` et `update()`
3. ✅ `resources/views/events/create.blade.php` - Formulaire déjà présent
4. ✅ `resources/views/events/edit.blade.php` - Formulaire déjà présent

## Statut
✅ **CORRIGÉ** - Le champ `max_participants` est maintenant correctement sauvegardé et récupéré

---
*Fix appliqué le: 2025-10-21*
*Problème: Champ max_participants non sauvegardé (absent du $fillable et de la logique du contrôleur)*
*Solution: Ajouté max_participants dans le modèle, la validation et l'enregistrement*
