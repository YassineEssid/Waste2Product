# 🔧 Fix: Erreur CO2_SAVED Dashboard Admin

## 🐛 Problème Rencontré

### Erreur :
```
SQLSTATE[42S22]: Column not found: 1054 Champ 'co2_saved' inconnu dans field list
SQL: select sum(`co2_saved`) as aggregate from `transformations`
```

### Contexte :
L'erreur se produisait lors de l'accès au dashboard admin (`/admin`) après connexion avec un compte administrateur.

---

## 🔍 Diagnostic

### Cause Racine :
La structure de la table `transformations` a été modifiée par la migration `2025_10_21_120414_add_missing_fields_to_transformations_table.php`.

**Ancienne structure** (migration 2025_09_25) :
```php
$table->decimal('co2_saved', 8, 2)->default(0);
$table->decimal('waste_reduced', 8, 2)->default(0);
```

**Nouvelle structure** (migration 2025_10_21) :
```php
$table->json('impact')->nullable(); // {co2_saved, waste_reduced}
```

### État des Migrations :
```
✅ 2025_09_25_125511_create_transformations_table ............. Ran (mais colonnes modifiées)
✅ 2025_10_21_120414_add_missing_fields_to_transformations_table ... Ran
```

La migration du 21 octobre a **converti** les colonnes séparées `co2_saved` et `waste_reduced` en une seule colonne JSON `impact`.

---

## ✅ Solution Appliquée

### Fichier : `app/Http/Controllers/Admin/AdminController.php`

**Avant** (ligne 48-51) :
```php
// Environmental impact
$environmentalStats = [
    'total_co2_saved' => Transformation::sum('co2_saved') ?? 0, // ❌ Colonne inexistante
    'total_waste_reduced' => WasteItem::count() * 2.5,
];
```

**Après** (ligne 48-57) :
```php
// Environmental impact
// Note: impact is a JSON column with structure: {co2_saved: X, waste_reduced: Y}
$transformations = Transformation::whereNotNull('impact')->get();
$totalCo2Saved = $transformations->sum(function($t) {
    $impact = is_string($t->impact) ? json_decode($t->impact, true) : $t->impact;
    return isset($impact['co2_saved']) ? (float)$impact['co2_saved'] : 0;
});

$environmentalStats = [
    'total_co2_saved' => $totalCo2Saved,
    'total_waste_reduced' => WasteItem::count() * 2.5, // 2.5kg per item estimation
];
```

### Explication du Code :

1. **Récupération des transformations** avec `impact` non null
```php
$transformations = Transformation::whereNotNull('impact')->get();
```

2. **Calcul de la somme** en parsant le JSON :
```php
$totalCo2Saved = $transformations->sum(function($t) {
    // Décoder le JSON si nécessaire (Laravel peut auto-caster)
    $impact = is_string($t->impact) ? json_decode($t->impact, true) : $t->impact;
    
    // Extraire co2_saved du tableau associatif
    return isset($impact['co2_saved']) ? (float)$impact['co2_saved'] : 0;
});
```

3. **Structure JSON attendue** dans la colonne `impact` :
```json
{
    "co2_saved": 15.5,
    "waste_reduced": 3.2
}
```

---

## 🔧 Alternative : Utiliser un Cast Eloquent

### Option 1 : Cast dans le Modèle (Recommandé)

Ajouter dans `app/Models/Transformation.php` :

```php
protected $casts = [
    'impact' => 'array',
    'before_images' => 'array',
    'after_images' => 'array',
    'process_images' => 'array',
];
```

Ensuite, le contrôleur peut être simplifié :
```php
$transformations = Transformation::whereNotNull('impact')->get();
$totalCo2Saved = $transformations->sum(function($t) {
    return $t->impact['co2_saved'] ?? 0;
});
```

### Option 2 : Utiliser whereJsonContains (Laravel 11)

```php
$totalCo2Saved = Transformation::whereNotNull('impact')
    ->get()
    ->sum(function($t) {
        return $t->impact['co2_saved'] ?? 0;
    });
```

---

## 📊 Structure de la Table `transformations`

### Colonnes Actuelles :
```sql
id                   BIGINT UNSIGNED
user_id              BIGINT UNSIGNED (FK)
waste_item_id        BIGINT UNSIGNED (FK)
title                VARCHAR(255)
description          TEXT
impact               JSON                 ← Nouveau (contient co2_saved + waste_reduced)
price                DECIMAL(10,2)
before_images        JSON
after_images         JSON
process_images       JSON
time_spent_hours     INT
materials_cost       DECIMAL(10,2)
status               ENUM('planned', 'pending', 'in_progress', 'completed', 'published')
is_featured          BOOLEAN
views_count          INT
created_at           TIMESTAMP
updated_at           TIMESTAMP
```

### Exemple de Données :
```sql
INSERT INTO transformations (
    user_id, waste_item_id, title, description, 
    impact, price, status
) VALUES (
    1, 1, 'Table from Pallets', 'Upcycled dining table',
    '{"co2_saved": 15.5, "waste_reduced": 3.2}', 
    120.00, 'published'
);
```

---

## 🧪 Test de la Correction

### 1. Accéder au Dashboard Admin
```
URL: http://127.0.0.1:8000/admin
```

### 2. Vérifications :
- ✅ Page charge sans erreur
- ✅ Carte "CO₂ Économisé" affiche une valeur
- ✅ Valeur = somme de tous les `impact.co2_saved`
- ✅ Si aucune transformation avec impact, affiche 0 kg

### 3. Tester avec Données :

**Créer une transformation avec impact** :
```php
php artisan tinker

$transformation = new App\Models\Transformation();
$transformation->user_id = 1;
$transformation->waste_item_id = 1;
$transformation->title = "Test Transformation";
$transformation->description = "Test";
$transformation->impact = ['co2_saved' => 25.5, 'waste_reduced' => 5.0];
$transformation->status = 'published';
$transformation->save();

// Vérifier
App\Models\Transformation::whereNotNull('impact')->get()->sum(function($t) {
    return $t->impact['co2_saved'] ?? 0;
});
// Devrait afficher: 25.5
```

### 4. Vérifier dans le Dashboard :
- Rafraîchir `/admin`
- La carte "CO₂ Économisé" devrait afficher **25.5 kg**

---

## 🛡️ Prévention des Erreurs Futures

### 1. Ajouter un Cast dans le Modèle

**Fichier** : `app/Models/Transformation.php`

```php
class Transformation extends Model
{
    protected $casts = [
        'impact' => 'array',
        'before_images' => 'array',
        'after_images' => 'array',
        'process_images' => 'array',
    ];
    
    // Accesseur pour co2_saved
    public function getCo2SavedAttribute()
    {
        return $this->impact['co2_saved'] ?? 0;
    }
    
    // Accesseur pour waste_reduced
    public function getWasteReducedAttribute()
    {
        return $this->impact['waste_reduced'] ?? 0;
    }
}
```

Ensuite, dans le contrôleur :
```php
$totalCo2Saved = Transformation::whereNotNull('impact')->get()->sum('co2_saved');
```

### 2. Validation lors de la Création

**Fichier** : `app/Http/Controllers/TransformationController.php`

```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'impact.co2_saved' => 'nullable|numeric|min:0',
    'impact.waste_reduced' => 'nullable|numeric|min:0',
]);
```

---

## 📝 Migration de Données (Si Nécessaire)

Si vous avez des anciennes données avec `co2_saved` et `waste_reduced` séparés, créez une migration de conversion :

```php
// database/migrations/2025_10_23_convert_co2_data.php

public function up()
{
    DB::table('transformations')->get()->each(function ($transformation) {
        // Si vous avez encore des colonnes co2_saved/waste_reduced
        $impact = [
            'co2_saved' => $transformation->co2_saved ?? 0,
            'waste_reduced' => $transformation->waste_reduced ?? 0,
        ];
        
        DB::table('transformations')
            ->where('id', $transformation->id)
            ->update(['impact' => json_encode($impact)]);
    });
}
```

---

## ✅ Résumé de la Correction

| Aspect | Avant | Après |
|--------|-------|-------|
| **Colonne** | `co2_saved` (DECIMAL) | `impact` (JSON) |
| **Requête** | `sum('co2_saved')` | `sum(function($t) { return $t->impact['co2_saved'] ?? 0; })` |
| **Erreur** | ❌ Column not found | ✅ Fonctionne |
| **Dashboard** | ❌ Internal Server Error | ✅ Affiche correctement |

---

## 🔗 Fichiers Modifiés

1. ✅ `app/Http/Controllers/Admin/AdminController.php` (lignes 48-57)

## 🔗 Fichiers à Modifier (Optionnel)

1. `app/Models/Transformation.php` (ajouter casts + accesseurs)
2. `app/Http/Controllers/TransformationController.php` (validation)

---

## 📚 Références

- **Laravel JSON Columns** : https://laravel.com/docs/11.x/eloquent-mutators#array-and-json-casting
- **Migration** : `2025_10_21_120414_add_missing_fields_to_transformations_table.php`
- **Modèle** : `app/Models/Transformation.php`

---

**✅ Problème Résolu ! Le dashboard admin fonctionne maintenant correctement.**

**Date de correction** : 2025-10-23  
**Commit** : Fix CO2 calculation in admin dashboard (JSON impact column)
