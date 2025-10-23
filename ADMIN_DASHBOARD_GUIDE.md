# 📊 Guide du Dashboard Administrateur - Waste2Product

## 🎯 Vue d'ensemble

Le dashboard administrateur offre une vue complète et interactive de la plateforme Waste2Product avec des statistiques en temps réel, des graphiques Chart.js et un suivi de l'activité.

---

## ✨ Fonctionnalités Principales

### 1. **Cartes Statistiques KPI** (4 cartes animées)

#### 🔹 Total Utilisateurs
- **Métrique principale** : Nombre total d'utilisateurs inscrits
- **Indicateur secondaire** : Nouveaux utilisateurs ce mois
- **Détail supplémentaire** : +X nouveaux cette semaine
- **Couleur** : Bleu (border-left: 4px)
- **Animation** : Hover effect (translateY -5px)

#### 🔹 Articles Déchets
- **Métrique principale** : Total d'articles déclarés
- **Indicateur secondaire** : Articles disponibles
- **Couleur** : Vert
- **Impact** : Contribution à la réduction des déchets

#### 🔹 Transformations
- **Métrique principale** : Total de transformations créées
- **Indicateur secondaire** : Transformations publiées
- **Couleur** : Violet
- **Utilité** : Mesure l'engagement des artisans

#### 🔹 CO₂ Économisé
- **Métrique principale** : Kilogrammes de CO₂ sauvés
- **Source** : Calculé depuis `transformations.co2_saved`
- **Couleur** : Jaune/Doré
- **Impact environnemental** : KPI clé de la plateforme

---

### 2. **Section Utilisateurs par Rôle**

#### Répartition des rôles :
- 👥 **Utilisateurs** (Vert) : Membres de la communauté
- ⚙️ **Réparateurs** (Jaune) : Experts en réparation
- 🎨 **Artisans** (Violet) : Créateurs et transformateurs
- 🛡️ **Administrateurs** (Rouge) : Gestionnaires de la plateforme

**Données affichées** :
- Nombre par rôle
- Description du rôle
- Icône distinctive
- Compteur en temps réel

---

### 3. **Activité de la Plateforme**

#### 📋 Statistiques détaillées :
- **Demandes de réparation**
  - Total + En attente
  - Border-left: Bleu
  
- **Événements communautaires**
  - Total + À venir
  - Border-left: Vert
  
- **Articles Marketplace**
  - Total en vente
  - Border-left: Violet
  
- **Déchets réduits**
  - Estimation en kg (2.5kg par item)
  - Border-left: Jaune

---

### 4. **📈 Graphiques Interactifs (Chart.js)**

#### **A. Croissance des Utilisateurs** (Line Chart)
```javascript
Type: 'line'
Période: 6 derniers mois
Données: Nouveaux utilisateurs par mois
Couleur: Bleu (rgb(59, 130, 246))
Options:
  - Fill: true (zone sous la courbe)
  - Tension: 0.4 (courbe lisse)
  - Point radius: 5 (points visibles)
  - Hover radius: 7
```

**SQL utilisée** :
```sql
SELECT 
    DATE_FORMAT(created_at, "%Y-%m") as month,
    COUNT(*) as count
FROM users
WHERE created_at >= NOW() - INTERVAL 6 MONTH
GROUP BY month
ORDER BY month
```

#### **B. Catégories Marketplace** (Doughnut Chart)
```javascript
Type: 'doughnut'
Données: Top 6 catégories par nombre d'articles
Couleurs: Palette de 6 couleurs (Bleu, Vert, Violet, Jaune, Rouge, Rose)
Position légende: 'right'
```

**SQL utilisée** :
```sql
SELECT 
    category,
    COUNT(*) as count
FROM marketplace_items
WHERE category IS NOT NULL
GROUP BY category
ORDER BY count DESC
LIMIT 6
```

#### **C. Inscriptions aux Événements** (Bar Chart)
```javascript
Type: 'bar'
Période: 6 derniers mois
Données: Nombre d'inscriptions par mois
Couleur: Violet (rgba(139, 92, 246, 0.8))
Border radius: 8 (coins arrondis)
```

**SQL utilisée** :
```sql
SELECT 
    DATE_FORMAT(community_events.starts_at, "%Y-%m") as month,
    COUNT(*) as registrations
FROM event_registrations
JOIN community_events ON event_registrations.event_id = community_events.id
WHERE community_events.starts_at >= NOW() - INTERVAL 6 MONTH
GROUP BY month
ORDER BY month
```

---

### 5. **🔄 Sections d'Activité Récente** (3 colonnes)

#### **A. Articles Marketplace Récents**
- **Affichage** : 5 derniers articles
- **Données** :
  - Titre (limité à 30 caractères)
  - Nom du vendeur
  - Statut (Disponible/Vendu)
  - Prix
- **Couleurs statut** :
  - Disponible : Vert
  - Vendu : Jaune

#### **B. Transformations Récentes**
- **Affichage** : 5 dernières transformations
- **Données** :
  - Titre (limité à 30 caractères)
  - Nom de l'utilisateur
  - Statut (Published/Draft/etc.)
- **Badge statut** :
  - Published : bg-green-100 text-green-800
  - Autres : bg-gray-100 text-gray-800

#### **C. Top Artisans**
- **Affichage** : Top 5 artisans par nombre de transformations
- **Données** :
  - Nom de l'artisan
  - Nombre de transformations
  - Avatar (initiale)
  - Icône étoile (classement)
- **Tri** : Par `transformations_count DESC`

---

### 6. **👥 Tableau des Derniers Utilisateurs**

**Colonnes** :
1. **Utilisateur**
   - Avatar (image ou initiale)
   - Nom
   - Email

2. **Rôle**
   - Badge coloré selon le rôle
   - Texte en majuscule

3. **Date d'inscription**
   - Format : dd/mm/YYYY
   - Temps relatif (diffForHumans)

4. **Actions**
   - Lien "Voir le profil" → `admin.users.show`

**Features** :
- Hover effect sur les lignes
- Responsive (overflow-x-auto)
- 5 utilisateurs affichés
- Lien "Voir tous" vers la liste complète

---

## 🎨 Design & Animations

### Animations CSS :
```css
/* Fade In Up Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stat Card Hover */
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}
```

### Délais d'animation :
- Carte 1 (Utilisateurs) : 0s
- Carte 2 (Déchets) : 0.1s
- Carte 3 (Transformations) : 0.2s
- Carte 4 (CO₂) : 0.3s

### Palette de couleurs :
- **Bleu** : `#3B82F6` (Utilisateurs, Liens)
- **Vert** : `#10B981` (Succès, Disponible)
- **Violet** : `#8B5CF6` (Artisans, Marketplace)
- **Jaune** : `#FBBF24` (Réparateurs, Impact)
- **Rouge** : `#EF4444` (Admin, Alertes)

---

## 🛠️ Architecture Technique

### Contrôleur : `AdminController.php`

```php
public function index()
{
    // 1. Statistiques utilisateurs (7 métriques)
    $userStats = [
        'total', 'users', 'repairers', 'artisans', 'admins',
        'new_this_month', 'new_this_week'
    ];

    // 2. Statistiques de contenu (11 métriques)
    $contentStats = [
        'waste_items', 'available_items', 
        'repair_requests', 'pending_repairs',
        'transformations', 'published_transformations',
        'community_events', 'upcoming_events',
        'marketplace_items', 'marketplace_available', 'marketplace_sold'
    ];

    // 3. Impact environnemental (2 métriques)
    $environmentalStats = [
        'total_co2_saved', 'total_waste_reduced'
    ];

    // 4. Données pour les graphiques
    - $userGrowth (6 mois)
    - $marketplaceCategories (top 6)
    - $eventRegistrations (6 mois)

    // 5. Activité récente
    - $recentUsers (5)
    - $recentMarketplaceItems (5)
    - $recentTransformations (5)
    - $topArtisans (5)

    return view('admin.dashboard', compact(...));
}
```

### Vue : `dashboard.blade.php`

**Structure** :
```blade
@extends('admin.layout')

@push('styles')
    <!-- CSS personnalisé -->
@endpush

@section('content')
    <!-- 1. KPI Cards (4) -->
    <!-- 2. Users by Role + Platform Activity -->
    <!-- 3. Charts (3 graphiques Chart.js) -->
    <!-- 4. Recent Activity (3 sections) -->
    <!-- 5. Recent Users Table -->
@endsection

@push('scripts')
    <!-- Chart.js CDN -->
    <!-- JavaScript pour les 3 graphiques -->
@endpush
```

### Bibliothèques externes :
- **Tailwind CSS** : Framework CSS
- **Chart.js 4.4.0** : Graphiques interactifs
- **Heroicons** : Icônes SVG (via Tailwind)

---

## 📊 Métriques et KPIs

### Métriques principales :
1. **Utilisateurs actifs** : Total et par rôle
2. **Croissance** : Nouveaux utilisateurs (mois/semaine)
3. **Engagement** : Transformations, Événements, Marketplace
4. **Impact** : CO₂ économisé, Déchets réduits

### KPIs à surveiller :
- **Taux de croissance** : +X utilisateurs/mois
- **Taux de publication** : Transformations publiées vs draft
- **Taux de vente** : Articles vendus vs disponibles
- **Participation** : Inscriptions aux événements
- **Impact environnemental** : CO₂ et déchets réduits

---

## 🚀 Utilisation

### Accès au dashboard :
```
URL: /admin
Route: admin.dashboard
Middleware: ['auth', 'role:admin']
```

### Navigation :
- **Sidebar gauche** : Menu principal admin
  - Dashboard (page actuelle)
  - Utilisateurs
  - Statistiques
  - Mon profil
  - Retour au site

### Actions rapides :
1. **Voir tous les utilisateurs** : Lien en haut du tableau
2. **Voir un profil** : Clic sur "Voir le profil" dans le tableau
3. **Filtrer par période** : Graphiques sur 6 mois (personnalisable)

---

## 🔧 Personnalisation

### Modifier la période des graphiques :
```php
// Dans AdminController.php, ligne 35
->where('created_at', '>=', now()->subMonths(6)) // Changer 6 en X mois
```

### Ajouter un graphique :
1. **Préparer les données** dans `AdminController@index`
2. **Créer un canvas** dans `dashboard.blade.php`
3. **Initialiser Chart.js** dans `@push('scripts')`

### Exemple d'ajout d'un graphique :
```javascript
const myCtx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(myCtx, {
    type: 'bar', // line, pie, doughnut, radar, etc.
    data: {
        labels: {!! json_encode($myData->pluck('label')) !!},
        datasets: [{
            label: 'Mon Dataset',
            data: {!! json_encode($myData->pluck('value')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
```

---

## 📱 Responsive Design

### Breakpoints Tailwind :
- **Mobile** : < 768px (md)
  - KPI cards : 1 colonne
  - Sidebar : Masquée
  - Graphiques : Stack vertical

- **Tablet** : 768px - 1024px (md-lg)
  - KPI cards : 2 colonnes
  - Graphiques : 1 colonne

- **Desktop** : > 1024px (lg)
  - KPI cards : 4 colonnes
  - Graphiques : 2 colonnes
  - Sidebar : Visible

---

## 🐛 Dépannage

### Problème : Graphiques ne s'affichent pas
**Solution** :
1. Vérifier que Chart.js CDN est chargé
2. Vérifier la console pour erreurs JavaScript
3. S'assurer que les données existent (`$userGrowth`, etc.)

### Problème : "Collection method pluck() undefined"
**Solution** :
```php
// Convertir en collection si nécessaire
$data = collect($data);
```

### Problème : Erreur 500 sur le dashboard
**Solution** :
1. Vérifier les relations Eloquent (User → transformations)
2. Vérifier les données nullable dans les statistiques
3. Activer le mode debug : `APP_DEBUG=true` dans `.env`

---

## 📈 Performances

### Optimisations appliquées :
1. **Eager Loading** : `with('seller')`, `with('user')`
2. **Limit queries** : `take(5)`, `LIMIT 6`
3. **Caching** (recommandé) :
```php
$userStats = Cache::remember('admin.user_stats', 300, function() {
    return [...];
});
```

### Temps de chargement cible :
- **Sans cache** : < 500ms
- **Avec cache** : < 100ms

---

## 🎓 Bonnes Pratiques

### 1. **Mise à jour régulière**
- Actualiser les données toutes les 5 minutes avec cache
- Utiliser des jobs pour les calculs lourds

### 2. **Accessibilité**
- Ajouter des `aria-label` sur les graphiques
- Utiliser des contrastes de couleurs suffisants

### 3. **Sécurité**
- Toujours vérifier le middleware `role:admin`
- Valider les données avant affichage

### 4. **Maintenance**
- Documenter les nouvelles métriques
- Tester après chaque modification

---

## 📚 Références

### Documentation :
- **Chart.js** : https://www.chartjs.org/docs/latest/
- **Tailwind CSS** : https://tailwindcss.com/docs
- **Laravel Eloquent** : https://laravel.com/docs/11.x/eloquent

### Fichiers liés :
- `app/Http/Controllers/Admin/AdminController.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/layout.blade.php`
- `routes/web.php` (ligne admin.dashboard)

---

## ✅ Checklist de Test

- [ ] Dashboard accessible via `/admin`
- [ ] Les 4 KPI cards s'affichent correctement
- [ ] Graphique de croissance utilisateurs fonctionne
- [ ] Graphique catégories marketplace fonctionne
- [ ] Graphique inscriptions événements fonctionne
- [ ] Sections d'activité récente affichent les données
- [ ] Tableau des utilisateurs affiche 5 utilisateurs
- [ ] Lien "Voir tous" fonctionne
- [ ] Animations hover sur les cartes
- [ ] Responsive sur mobile/tablet/desktop
- [ ] Aucune erreur dans la console
- [ ] Performance < 500ms

---

## 🎉 Résultat Final

Le dashboard administrateur Waste2Product offre maintenant :
- ✅ **14+ KPIs** en temps réel
- ✅ **3 graphiques interactifs** Chart.js
- ✅ **5 sections d'activité** récente
- ✅ **Design moderne** avec animations
- ✅ **100% responsive**
- ✅ **Performance optimisée**

**Prêt à l'emploi ! 🚀**

---

**Créé le** : <?php echo date('Y-m-d H:i:s'); ?>  
**Version** : 1.0.0  
**Auteur** : Waste2Product Development Team
