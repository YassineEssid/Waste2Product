# Gamification System - Waste2Product

## 🎮 Overview

Complete gamification system integrated into Waste2Product platform to increase user engagement through points, badges, levels, and leaderboards.

## ✨ Features

### 1. **Points System**
Users earn points by participating in the community:
- **Event Created**: 50 points
- **Event Attended**: 30 points
- **Comment Posted**: 10 points
- **Comment Approved**: +5 bonus points
- **Rating Given**: 5 points
- **Waste Item Posted**: 15 points
- **Marketplace Item Sold**: 40 points
- **Transformation Completed**: 60 points
- **Repair Completed**: 50 points
- **Registration**: 20 points
- **Profile Completed**: 25 points

### 2. **Badge System**
20 predefined badges across 4 rarity levels:
- **Common** (Rarity 1): First Event, First Comment, First Waste Post, First Transformation, First Repair
- **Rare** (Rarity 2): Social Butterfly, Talkative, Waste Hunter, Level 5 & 10
- **Epic** (Rarity 3): Event Master, Comment Master, Eco Warrior, Master Repairer, Level 20
- **Legendary** (Rarity 4): Community Champion, Transformation Legend, Level 30 & 50

### 3. **Level System**
- **Level Progression**: 100 points per level
- **Automatic Titles**:
  - Level 1-4: Eco Beginner
  - Level 5-9: Green Contributor
  - Level 10-19: Eco Enthusiast
  - Level 20-29: Environmental Expert
  - Level 30-39: Green Champion
  - Level 40-49: Sustainability Master
  - Level 50+: Eco Legend

### 4. **Leaderboard**
- Global rankings (all-time, monthly, weekly)
- Category-specific leaderboards (events, comments, marketplace)
- Top 3 podium display
- User rank tracking

## 📁 File Structure

```
app/
├── Models/
│   ├── Badge.php                    # Badge definitions
│   ├── UserBadge.php                # User badge ownership
│   ├── PointTransaction.php         # Point transaction history
│   └── User.php                     # Updated with gamification
├── Services/
│   └── GamificationService.php      # Business logic
└── Http/Controllers/
    ├── BadgeController.php          # Badge CRUD
    ├── LeaderboardController.php    # Rankings
    └── GamificationController.php   # User stats

database/
├── migrations/
│   ├── 2025_10_18_125050_create_badges_table.php
│   ├── 2025_10_18_125050_create_user_badges_table.php
│   ├── 2025_10_18_125051_create_point_transactions_table.php
│   └── 2025_10_18_125052_add_gamification_fields_to_users_table.php
└── seeders/
    └── BadgesSeeder.php             # 20 predefined badges

resources/views/
├── badges/
│   ├── index.blade.php              # All badges grid
│   ├── show.blade.php               # Badge details
│   └── collection.blade.php         # User's badge collection
├── leaderboard/
│   └── index.blade.php              # Rankings table
└── gamification/
    ├── profile.blade.php            # User gamification profile
    └── points-info.blade.php        # Points information
```

## 🚀 Installation

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Seed Badges
```bash
php artisan db:seed --class=BadgesSeeder
```

### Step 3: Clear Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## 🔌 Integration Points

### EventCommentController
Points awarded when:
- User posts a comment: **10 points**
- User gives a rating: **+5 points**
- Admin approves comment: **+5 points** to comment author

### CommunityEventController
Points awarded when:
- User creates an event: **50 points**
- User registers for an event: **30 points**

## 📊 Database Schema

### badges
- `id`, `name`, `slug`, `description`, `icon`, `image`, `color`
- `type` (event/comment/participation/achievement/special)
- `required_points`, `required_count`, `requirement_type`
- `rarity` (1-4), `is_active`

### user_badges
- `id`, `user_id`, `badge_id`
- `earned_at`, `is_displayed`, `progress`

### point_transactions
- `id`, `user_id`, `points`, `type`, `description`
- `reference_type`, `reference_id` (polymorphic)
- `balance_after`

### users (new fields)
- `total_points`, `current_level`, `points_to_next_level`
- `title`, `last_point_earned_at`

## 🎯 API Routes

### Badges
- `GET /badges` - List all badges
- `GET /badges/{id}` - Badge details
- `GET /badges/collection` - User's badge collection
- `POST /badges/{id}/toggle-display` - Toggle badge display

### Leaderboard
- `GET /leaderboard` - Rankings (all/month/week)
- `GET /leaderboard/user/{id}` - User profile

### Gamification
- `GET /gamification/profile` - User gamification profile
- `GET /gamification/activity` - Activity history
- `GET /gamification/achievements` - Achievements overview
- `GET /gamification/points-info` - Points information

## 💡 Usage Examples

### Award Points
```php
use App\Services\GamificationService;

$gamificationService = app(GamificationService::class);

// Award points
$gamificationService->awardPoints(
    $user,
    'event_created',
    'Created event: Community Cleanup',
    $event
);
```

### Check Badge Progress
```php
// Automatic badge checking after awarding points
$gamificationService->checkBadgeProgress($user, 'events_created');
```

### Get User Stats
```php
$stats = $gamificationService->getUserStats($user);
// Returns: total_points, current_level, rank, badges_earned, etc.
```

### Get Leaderboard
```php
$topUsers = $gamificationService->getLeaderboard(50, 'week');
```

## 🎨 UI Components

All views feature:
- Modern gradient hero sections
- Responsive design (mobile/tablet/desktop)
- Hover animations and transitions
- Font Awesome 6 icons
- Bootstrap 5 cards and badges
- Progress bars for badge tracking
- Statistics cards
- Filter systems

## 🔒 Security

- All routes protected with `auth` middleware
- Badge display toggle requires authentication
- Admin-only approval systems
- SQL injection prevention via Eloquent ORM
- XSS protection in Blade templates

## 📈 Performance

- Eager loading relationships
- Database indexes on foreign keys
- Efficient queries with proper scoping
- Pagination for large datasets

## 🧪 Testing

To test the system:
1. Create a test user
2. Create an event (+50 pts)
3. Register for an event (+30 pts)
4. Post a comment (+10 pts)
5. Check your profile at `/gamification/profile`
6. View leaderboard at `/leaderboard`
7. Explore badges at `/badges`

## 🔄 Future Enhancements

Potential additions:
- Daily/weekly challenges
- Team-based competitions
- Seasonal events
- Achievement notifications
- Badge trading system
- Reputation system
- Milestone rewards
- Social sharing of achievements

## 📝 Notes

- Points are automatically awarded via service methods
- Badges are checked automatically after point awards
- Levels update automatically when point thresholds are reached
- All transactions are logged for audit purposes
- Users can track their progress in real-time

## 🐛 Troubleshooting

### Points not awarded?
- Check if GamificationService is properly injected
- Verify point type exists in POINTS constant
- Check transaction logs in point_transactions table

### Badges not unlocking?
- Verify badge requirements match user progress
- Check if badge is active (is_active = 1)
- Run: `$gamificationService->checkBadgeProgress($user, 'type')`

### Leaderboard not showing?
- Clear cache: `php artisan cache:clear`
- Check if users have total_points > 0
- Verify date filters are correct

## 📞 Support

For issues or questions:
- Check documentation in `/docs`
- Review code comments
- Check Laravel logs in `storage/logs`

---

**Created by:** Waste2Product Team  
**Date:** October 18, 2025  
**Version:** 1.0.0
