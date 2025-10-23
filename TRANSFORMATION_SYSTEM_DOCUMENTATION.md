# Système de Gestion des Transformations - Documentation Complète

## Vue d'ensemble

Le système de gestion des transformations permet aux artisans de transformer les déchets en produits de valeur avec un suivi complet du processus, de l'impact environnemental et une publication automatique sur le marketplace.

## Architecture

### Entités principales

#### 1. **Transformation**
- **Table**: `transformations` (20 colonnes)
- **Modèle**: `App\Models\Transformation`
- **Contrôleur**: `TransformationController` (368 lignes)
- **Vues**: 4 fichiers Blade (index, create, show, edit)

#### 2. **MarketplaceItem** (Entité liée)
- Création automatique lors de la publication d'une transformation
- Relation: Une transformation peut créer un article marketplace

### Structure de la table Transformations

```sql
CREATE TABLE transformations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    waste_item_id BIGINT UNSIGNED NOT NULL,
    artisan_id BIGINT UNSIGNED NOT NULL,
    product_title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    impact JSON NULL,  -- {co2_saved: decimal, waste_reduced: decimal}
    price DECIMAL(10,2) NULL,
    before_image VARCHAR(255) NULL,
    before_images JSON NULL,  -- Array of image paths
    after_image VARCHAR(255) NULL,
    after_images JSON NULL,   -- Array of image paths
    process_images JSON NULL, -- Array of image paths
    time_spent_hours INT NULL,
    materials_cost DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('planned', 'pending', 'in_progress', 'completed', 'published') NOT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    views_count INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (waste_item_id) REFERENCES waste_items(id),
    FOREIGN KEY (artisan_id) REFERENCES users(id)
) ENGINE=MyISAM;
```

## Workflow de transformation

### Statuts disponibles

1. **planned** (Planifiée) - Badge bleu info
   - Transformation planifiée mais pas encore commencée
   
2. **in_progress** (En cours) - Badge jaune warning
   - Travail de transformation en cours
   
3. **completed** (Terminée) - Badge bleu primary
   - Transformation terminée, prête à être publiée
   
4. **published** (Publiée) - Badge vert success
   - Automatiquement publiée sur le marketplace
   - Création automatique d'un MarketplaceItem

### Processus complet

```
┌─────────────┐
│   planned   │  Artisan planifie la transformation
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ in_progress │  Artisan travaille sur la transformation
└──────┬──────┘  - Upload des process_images
       │          - Suivi du temps passé
       ▼
┌─────────────┐
│  completed  │  Transformation terminée
└──────┬──────┘  - Upload des after_images
       │          - Définition du price final
       ▼
┌─────────────┐
│  published  │  Publication automatique sur Marketplace
└─────────────┘  - Création MarketplaceItem
                 - Images copiées (after_images)
                 - Status = 'available'
```

## Fonctionnalités du contrôleur

### TransformationController

#### Méthodes principales

**1. index(Request $request)**
- Affiche la liste des transformations avec filtres
- Filtres: status, search, sort (created_at, price, views_count)
- Statistiques: total, planned, in_progress, completed, published
- Pagination: 12 items par page

**2. create()**
- Formulaire de création
- Restriction: Seuls les artisans peuvent créer
- Liste déroulante des déchets disponibles (status='available')

**3. store(Request $request)**
- Validation complète des données
- Gestion des uploads multiples (before_images, after_images, process_images)
- Stockage dans `storage/transformations/[before|after|process]/`
- Mise à jour automatique du waste_item (status='transformed')
- Champs impact JSON: co2_saved, waste_reduced

**4. show(Transformation $transformation)**
- Affichage détaillé avec galerie d'images
- Tabs: Avant, Processus, Après
- Incrémentation automatique du views_count
- Impact environnemental visualisé
- Bouton "Publier sur Marketplace" si status='completed'

**5. edit(Transformation $transformation)**
- Formulaire de modification
- Autorisation: Propriétaire ou admin uniquement
- Préchargement de toutes les valeurs existantes
- Gestion de l'ajout/suppression d'images

**6. update(Request $request, Transformation $transformation)**
- Validation des modifications
- Gestion avancée des images:
  - Suppression d'images existantes (remove_before_images, remove_after_images, remove_process_images)
  - Ajout de nouvelles images
- **Trigger automatique**: Si status passe à 'published', appel de `publishToMarketplace()`

**7. destroy(Transformation $transformation)**
- Suppression de la transformation
- Autorisation: Propriétaire ou admin uniquement
- Suppression de toutes les images associées du storage

**8. publish(Transformation $transformation)**
- Route spéciale: POST /transformations/{id}/publish
- Restriction: status doit être 'completed'
- Mise à jour status → 'published'
- Appel de `publishToMarketplace()`

**9. publishToMarketplace(Transformation $transformation)** [Protected]
- Création automatique d'un MarketplaceItem
- Mapping des données:
  ```php
  seller_id: transformation->artisan_id
  name: transformation->product_title
  title: transformation->product_title
  description: transformation->description
  price: transformation->price
  category: 'recycled'
  condition: 'new'
  quantity: 1
  status: 'available'
  ```
- Création des images liées (MarketplaceItemImage):
  - Source: transformation->after_images
  - Table: marketplace_item_images
  - Colonne: image_path

## Validation des données

### Création (store)

```php
'waste_item_id' => 'required|exists:waste_items,id',
'product_title' => 'required|string|max:255',
'description' => 'required|string',
'price' => 'nullable|numeric|min:0',
'time_spent_hours' => 'nullable|integer|min:0',
'materials_cost' => 'nullable|numeric|min:0',
'co2_saved' => 'nullable|numeric|min:0',
'waste_reduced' => 'nullable|numeric|min:0',
'before_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
'after_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
'process_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
'status' => 'required|in:planned,in_progress,completed'
```

### Modification (update)

Identique à la création + champs supplémentaires:
```php
'status' => 'required|in:planned,in_progress,completed,published',
'remove_before_images' => 'nullable|array',
'remove_after_images' => 'nullable|array',
'remove_process_images' => 'nullable|array',
```

## Routes

8 routes configurées dans `routes/web.php`:

```php
GET    /transformations                              // index
POST   /transformations                              // store
GET    /transformations/create                       // create
GET    /transformations/{transformation}             // show
PUT    /transformations/{transformation}             // update
DELETE /transformations/{transformation}             // destroy
GET    /transformations/{transformation}/edit        // edit
POST   /transformations/{transformation}/publish     // publish (custom)
```

Toutes les routes nécessitent l'authentification (middleware: auth).

## Vues Blade

### 1. index.blade.php (273 lignes)

**Composants:**
- Header avec gradient coloré + bouton "Nouvelle Transformation"
- 6 cartes statistiques:
  - Total
  - Planifiées (info)
  - En cours (warning)
  - Terminées (success)
  - Publiées (primary)
  - CO₂ total économisé (success)
- Filtres:
  - Recherche textuelle
  - Statut (dropdown)
  - Tri (created_at, price, views_count)
  - Ordre (asc/desc)
- Grid responsive (3 colonnes sur desktop, 2 sur tablette, 1 sur mobile)
- Chaque carte transformation affiche:
  - Image principale (after_images[0] ou before_images[0])
  - Badge status coloré
  - Badge "Vedette" si is_featured
  - Titre (product_title)
  - Description tronquée (100 caractères)
  - Impact environnemental (CO₂, déchets réduits)
  - Prix
  - Nombre de vues
  - Boutons: Voir, Modifier (si propriétaire)
- Pagination Bootstrap
- Effet hover avec élévation

### 2. create.blade.php (207 lignes)

**Sections:**
1. **Informations de base**
   - Déchet source (select avec waste_items disponibles)
   - Titre du produit
   - Description (textarea 4 rows)
   - Prix (optionnel)
   - Statut (planned/in_progress/completed)

2. **Impact environnemental**
   - CO₂ économisé (kg)
   - Déchets réduits (kg)
   - Icônes illustratives

3. **Détails du processus**
   - Temps passé (heures)
   - Coût des matériaux (DT)

4. **Images**
   - Photos avant (multiple)
   - Photos du processus (multiple)
   - Photos après (multiple)
   - Accept: image/* (jpeg, png, jpg, gif)
   - Max: 2048 KB par image

**UX:**
- Retour arrière vers liste
- Validation en temps réel (@error directives)
- Boutons: Annuler, Créer la transformation

### 3. show.blade.php (265 lignes)

**Layout 8-4 (Desktop):**

**Colonne gauche (8/12):**
- Carrousel principal (500px height)
  - Toutes les images (before + process + after)
  - Contrôles prev/next si multiple
- Tabs avec compteurs:
  - Avant: miniatures before_images
  - Processus: miniatures process_images
  - Après: miniatures after_images
- Description complète

**Colonne droite (4/12):**
- Titre + Prix
- Badge status coloré + iconographique
- Badge "Vedette" si is_featured
- Vues + Date de création
- **Actions (si propriétaire):**
  - Bouton "Modifier"
  - Bouton "Publier sur Marketplace" (si completed)
  - Bouton "Supprimer" (confirmation)
- **Carte Impact Environnemental** (si données présentes):
  - CO₂ économisé (vert)
  - Déchets réduits (bleu)
  - Icônes circulaires
- **Carte Détails du Processus**:
  - Artisan (nom)
  - Déchet source
  - Temps passé
  - Coût des matériaux

**Fonctionnalités:**
- Incrémentation automatique du views_count à chaque visite
- Carrousel Bootstrap 5 responsive
- Tabs Bootstrap avec fade animation

### 4. edit.blade.php (208 lignes)

**Structure:**
- Identique à create.blade.php
- Différences:
  - Titre: "Modifier la Transformation"
  - URL form action: `transformations.update`
  - Méthode: PUT (via @method directive)
  - Valeurs préchargées: old() ou $transformation->champ
  - Retour vers show au lieu de index
  - Bouton: "Mettre à jour" au lieu de "Créer"
  - Status additionnel: 'published' dans le select
  - Support de la suppression d'images existantes (à implémenter côté frontend)

**Préchargements:**
```blade
value="{{ old('product_title', $transformation->product_title) }}"
value="{{ old('price', $transformation->price) }}"
{{ old('description', $transformation->description) }}
{{ old('co2_saved', $transformation->impact['co2_saved'] ?? 0) }}
{{ old('waste_reduced', $transformation->impact['waste_reduced'] ?? 0) }}
```

## Intégration Dashboard Artisan

### Modifications apportées

**Fichier**: `resources/views/dashboards/artisan.blade.php`

**Section "Mes transformations" améliorée:**
- Affichage de product_title au lieu de title
- Badges de statut colorés dynamiques:
  - planned → info (bleu clair)
  - in_progress → warning (jaune)
  - completed → primary (bleu)
  - published → success (vert)
- Traduction française des statuts
- Affichage du prix si défini
- Affichage de l'impact environnemental:
  - CO₂ économisé (vert, icône leaf)
  - Déchets réduits (bleu info, icône recycle)
- Deux boutons d'action:
  - Voir (œil)
  - Modifier (crayon)

**Layout:**
```html
<div class="card h-100 border-start border-purple border-4">
    <div class="card-body">
        <h6>product_title</h6>
        <span class="badge">Status traduit</span>
        <p>Description (80 chars)</p>
        <div>Prix</div>
        <div class="row">
            <div class="col-6">CO₂</div>
            <div class="col-6">Déchets</div>
        </div>
        <div class="d-flex">
            <small>Date</small>
            <div>
                <a>Voir</a>
                <a>Modifier</a>
            </div>
        </div>
    </div>
</div>
```

## Sécurité et Autorisations

### Règles d'accès

1. **Création (create/store)**
   - Réservé aux utilisateurs avec role='artisan'
   - Abort 403 si autre rôle

2. **Modification (edit/update)**
   - Propriétaire (artisan_id === auth()->id())
   - OU administrateur (role='admin')
   - Abort 403 sinon

3. **Suppression (destroy)**
   - Même règle que modification

4. **Publication (publish)**
   - Même règle que modification
   - + Vérification status='completed'

### Vérifications dans les vues

```blade
@if(auth()->user()->role === 'artisan')
    <!-- Bouton Nouvelle Transformation -->
@endif

@if($transformation->artisan_id === auth()->id())
    <!-- Boutons Modifier/Supprimer -->
@endif
```

## Impact Environnemental

### Structure JSON

Le champ `impact` stocke un objet JSON:

```json
{
    "co2_saved": 12.5,        // Kilogrammes de CO₂ économisés
    "waste_reduced": 8.3      // Kilogrammes de déchets réduits
}
```

### Calcul affiché

**Dashboard:**
- Somme totale: `Transformation::sum('impact->co2_saved')`
- Affichage: "X kg CO₂ économisés"

**Index:**
- Carte statistique verte avec icône leaf
- Total global des économies CO₂

**Show:**
- Carte dédiée "Impact Environnemental"
- Deux métriques avec icônes circulaires

## Gestion des Images

### Organisation du storage

```
storage/
└── app/
    └── public/
        └── transformations/
            ├── before/
            │   ├── abc123.jpg
            │   └── def456.png
            ├── after/
            │   ├── ghi789.jpg
            │   └── jkl012.jpg
            └── process/
                ├── mno345.jpg
                └── pqr678.png
```

### Stockage dans la base

Les champs `before_images`, `after_images`, `process_images` contiennent des arrays JSON:

```json
[
    "transformations/before/abc123.jpg",
    "transformations/before/def456.png"
]
```

### Affichage

```blade
@if($transformation->after_images && count($transformation->after_images) > 0)
    @foreach($transformation->after_images as $image)
        <img src="{{ asset('storage/' . $image) }}" alt="Après">
    @endforeach
@endif
```

### Suppression

Lors de la suppression d'une transformation:
```php
if ($transformation->before_images) {
    foreach ($transformation->before_images as $image) {
        Storage::disk('public')->delete($image);
    }
}
```

## Intégration Marketplace

### Méthode publishToMarketplace()

**Déclenchement:**
1. Méthode `update()` si status passe à 'published'
2. Méthode `publish()` (route dédiée)

**Logique:**
1. Vérification de l'existence (éviter les doublons)
2. Création du MarketplaceItem:
   ```php
   [
       'seller_id' => $transformation->artisan_id,
       'name' => $transformation->product_title,
       'title' => $transformation->product_title,
       'description' => $transformation->description,
       'price' => $transformation->price ?? 0,
       'category' => 'recycled',
       'condition' => 'new',
       'quantity' => 1,
       'status' => 'available',
   ]
   ```
3. Création des images associées:
   ```php
   foreach ($transformation->after_images as $index => $imagePath) {
       $marketplaceItem->images()->create([
           'image_path' => $imagePath,
           'order' => $index
       ]);
   }
   ```

**Points importants:**
- Seules les `after_images` sont copiées (images du produit fini)
- Les images ne sont pas dupliquées physiquement (même path)
- Le seller_id est lié à l'artisan_id de la transformation

## Tests et Validation

### Commandes utiles

**Vérifier les routes:**
```bash
php artisan route:list --name=transformation
```

**Compter les transformations:**
```bash
php artisan tinker --execute="echo App\Models\Transformation::count();"
```

**Vérifier la structure de la table:**
```bash
php artisan db:table transformations
```

**Tester la syntaxe PHP:**
```bash
php -l app/Http/Controllers/TransformationController.php
```

### Points de test

1. ✅ Création d'une transformation (artisan uniquement)
2. ✅ Upload de multiples images
3. ✅ Modification avec changement de status
4. ✅ Publication automatique sur marketplace
5. ✅ Création de MarketplaceItem + images liées
6. ✅ Sécurité: non-propriétaire ne peut pas modifier
7. ✅ Affichage de l'impact environnemental
8. ✅ Filtres et recherche sur index
9. ✅ Carrousel d'images sur show
10. ✅ Dashboard artisan avec nouvelles données

## Design et UX

### Palette de couleurs

**Statuts:**
- planned: `bg-info` (#17a2b8 - bleu info)
- in_progress: `bg-warning` (#ffc107 - jaune)
- completed: `bg-primary` (#007bff - bleu)
- published: `bg-success` (#28a745 - vert)

**Impact:**
- CO₂: `text-success` avec icône `fa-leaf` ou `fa-cloud`
- Déchets: `text-info` avec icône `fa-recycle` ou `fa-trash-alt`

**Artisan:**
- Couleur thème: `bg-purple` (#9b59b6)
- Bordure: `border-purple border-4`

### Composants Bootstrap 5

- Cards avec `shadow-sm` et `border-0`
- Badges avec `badge bg-*`
- Buttons avec `btn btn-*`
- Forms avec `form-control`, `form-select`
- Grid responsive avec `col-md-*`, `col-lg-*`
- Carousel avec `carousel slide`
- Tabs avec `nav nav-tabs` et `tab-content`

### Icônes Font Awesome 6

**Principales icônes utilisées:**
- `fa-recycle` - Recyclage/Transformation
- `fa-leaf` - Impact CO₂
- `fa-trash-alt` - Déchets
- `fa-coins` - Prix/Revenus
- `fa-check-circle` - Terminée
- `fa-spinner` - En cours
- `fa-clipboard-list` - Planifiée
- `fa-store` - Publiée/Marketplace
- `fa-eye` - Vues
- `fa-edit` - Modifier
- `fa-plus-circle` - Créer
- `fa-star` - Vedette

## Maintenance et Évolution

### Améliorations possibles

1. **Système de notation**
   - Permettre aux utilisateurs de noter les transformations

2. **Partage social**
   - Boutons de partage sur réseaux sociaux

3. **Statistiques avancées**
   - Graphiques d'évolution CO₂ économisé
   - Rapport mensuel des transformations

4. **Notifications**
   - Email quand transformation publiée sur marketplace
   - Alerte admin pour nouvelles transformations

5. **Système de favoris**
   - Users peuvent "liker" des transformations

6. **Export PDF**
   - Générer un certificat d'impact environnemental

7. **Vidéos du processus**
   - Support de vidéos en plus des images

8. **Recherche avancée**
   - Filtres par impact, prix, artisan

### Monitoring

**Métriques à surveiller:**
- Nombre de transformations par statut
- Taux de conversion completed → published
- CO₂ total économisé
- Temps moyen de transformation
- Coût moyen des matériaux
- Revenue moyen par transformation

## Dépendances

### Packages Laravel

- `illuminate/database` - Eloquent ORM
- `illuminate/http` - Controllers & Requests
- `illuminate/support` - Helpers & Facades
- `illuminate/validation` - Validation rules
- `intervention/image` - Gestion d'images (optionnel)

### Frontend

- Bootstrap 5.x
- Font Awesome 6.x
- Vanilla JavaScript (Bootstrap JS)

## Conclusion

Le système de gestion des transformations est maintenant complet et fonctionnel avec:

✅ **2 entités liées** (Transformation ↔ MarketplaceItem)
✅ **CRUD complet** avec validation et sécurité
✅ **20 colonnes** dans la table transformations
✅ **Impact environnemental** (JSON: co2_saved, waste_reduced)
✅ **Gestion d'images multiples** (before, process, after)
✅ **Workflow en 5 étapes** (planned → published)
✅ **Publication automatique** sur marketplace
✅ **4 vues modernes** avec Bootstrap 5
✅ **Dashboard artisan enrichi** avec statistiques et affichage détaillé
✅ **8 routes** configurées et testées
✅ **Autorisation granulaire** (artisan uniquement, propriétaire pour édition)

Le système respecte les bonnes pratiques Laravel et offre une expérience utilisateur optimale pour les artisans souhaitant transformer les déchets en produits de valeur.
