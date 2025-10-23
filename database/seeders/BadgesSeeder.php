<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BadgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Event Badges (type: event)
            [
                'name' => 'First Event',
                'slug' => 'first-event',
                'description' => 'Create your first community event. Start building the community!',
                'icon' => 'fa-flag',
                'color' => '#3b82f6',
                'type' => 'event',
                'required_points' => 0,
                'required_count' => 1,
                'requirement_type' => 'events_created',
                'rarity' => 1, // Common
                'is_active' => true,
            ],
            [
                'name' => 'Event Master',
                'slug' => 'event-master',
                'description' => 'Create 10 successful community events. You\'re a community leader!',
                'icon' => 'fa-calendar-check',
                'color' => '#8b5cf6',
                'type' => 'event',
                'required_points' => 0,
                'required_count' => 10,
                'requirement_type' => 'events_created',
                'rarity' => 3, // Epic
                'is_active' => true,
            ],
            [
                'name' => 'Social Butterfly',
                'slug' => 'social-butterfly',
                'description' => 'Attend 5 community events. Love meeting new eco-friends!',
                'icon' => 'fa-users',
                'color' => '#ec4899',
                'type' => 'participation',
                'required_points' => 0,
                'required_count' => 5,
                'requirement_type' => 'events_attended',
                'rarity' => 2, // Rare
                'is_active' => true,
            ],
            [
                'name' => 'Community Champion',
                'slug' => 'community-champion',
                'description' => 'Attend 25 community events. You\'re the heart of this community!',
                'icon' => 'fa-trophy',
                'color' => '#f59e0b',
                'type' => 'participation',
                'required_points' => 0,
                'required_count' => 25,
                'requirement_type' => 'events_attended',
                'rarity' => 4, // Legendary
                'is_active' => true,
            ],

            // Comment Badges (type: comment)
            [
                'name' => 'First Comment',
                'slug' => 'first-comment',
                'description' => 'Post your first comment on an event. Your voice matters!',
                'icon' => 'fa-comment',
                'color' => '#10b981',
                'type' => 'comment',
                'required_points' => 0,
                'required_count' => 1,
                'requirement_type' => 'comments_posted',
                'rarity' => 1, // Common
                'is_active' => true,
            ],
            [
                'name' => 'Talkative',
                'slug' => 'talkative',
                'description' => 'Post 25 comments. You love sharing your thoughts!',
                'icon' => 'fa-comments',
                'color' => '#06b6d4',
                'type' => 'comment',
                'required_points' => 0,
                'required_count' => 25,
                'requirement_type' => 'comments_posted',
                'rarity' => 2, // Rare
                'is_active' => true,
            ],
            [
                'name' => 'Comment Master',
                'slug' => 'comment-master',
                'description' => 'Post 100 comments. You\'re a discussion leader!',
                'icon' => 'fa-comment-dots',
                'color' => '#8b5cf6',
                'type' => 'comment',
                'required_points' => 0,
                'required_count' => 100,
                'requirement_type' => 'comments_posted',
                'rarity' => 3, // Epic
                'is_active' => true,
            ],

            // Transformation Badges (type: achievement)
            [
                'name' => 'First Transformation',
                'slug' => 'first-transformation',
                'description' => 'Complete your first waste transformation. Turn trash into treasure!',
                'icon' => 'fa-recycle',
                'color' => '#22c55e',
                'type' => 'achievement',
                'required_points' => 0,
                'required_count' => 1,
                'requirement_type' => 'transformations_completed',
                'rarity' => 1, // Common
                'is_active' => true,
            ],
            [
                'name' => 'Eco Warrior',
                'slug' => 'eco-warrior',
                'description' => 'Complete 10 waste transformations. Fighting waste like a pro!',
                'icon' => 'fa-leaf',
                'color' => '#16a34a',
                'type' => 'achievement',
                'required_points' => 0,
                'required_count' => 10,
                'requirement_type' => 'transformations_completed',
                'rarity' => 3, // Epic
                'is_active' => true,
            ],
            [
                'name' => 'Transformation Legend',
                'slug' => 'transformation-legend',
                'description' => 'Complete 50 waste transformations. You\'re a sustainability superhero!',
                'icon' => 'fa-star',
                'color' => '#fbbf24',
                'type' => 'achievement',
                'required_points' => 0,
                'required_count' => 50,
                'requirement_type' => 'transformations_completed',
                'rarity' => 4, // Legendary
                'is_active' => true,
            ],

            // Repair Badges (type: achievement)
            [
                'name' => 'First Repair',
                'slug' => 'first-repair',
                'description' => 'Complete your first repair request. Fixing things feels great!',
                'icon' => 'fa-wrench',
                'color' => '#6366f1',
                'type' => 'achievement',
                'required_points' => 0,
                'required_count' => 1,
                'requirement_type' => 'repairs_completed',
                'rarity' => 1, // Common
                'is_active' => true,
            ],
            [
                'name' => 'Master Repairer',
                'slug' => 'master-repairer',
                'description' => 'Complete 20 repair requests. You\'re a repair expert!',
                'icon' => 'fa-tools',
                'color' => '#7c3aed',
                'type' => 'achievement',
                'required_points' => 0,
                'required_count' => 20,
                'requirement_type' => 'repairs_completed',
                'rarity' => 3, // Epic
                'is_active' => true,
            ],

            // Waste Item Badges (type: achievement)
            [
                'name' => 'First Waste Post',
                'slug' => 'first-waste-post',
                'description' => 'Post your first waste item. Every journey starts with one step!',
                'icon' => 'fa-trash-restore',
                'color' => '#14b8a6',
                'type' => 'achievement',
                'required_points' => 0,
                'required_count' => 1,
                'requirement_type' => 'waste_items_posted',
                'rarity' => 1, // Common
                'is_active' => true,
            ],
            [
                'name' => 'Waste Hunter',
                'slug' => 'waste-hunter',
                'description' => 'Post 25 waste items. You have an eye for opportunity!',
                'icon' => 'fa-search',
                'color' => '#06b6d4',
                'type' => 'achievement',
                'required_points' => 0,
                'required_count' => 25,
                'requirement_type' => 'waste_items_posted',
                'rarity' => 2, // Rare
                'is_active' => true,
            ],

            // Level Badges (type: special)
            [
                'name' => 'Level 5 Pioneer',
                'slug' => 'level-5-badge',
                'description' => 'Reach level 5. You\'re making great progress!',
                'icon' => 'fa-certificate',
                'color' => '#3b82f6',
                'type' => 'special',
                'required_points' => 500,
                'required_count' => 0,
                'requirement_type' => 'level',
                'rarity' => 2, // Rare
                'is_active' => true,
            ],
            [
                'name' => 'Level 10 Expert',
                'slug' => 'level-10-badge',
                'description' => 'Reach level 10. You\'re becoming an expert!',
                'icon' => 'fa-medal',
                'color' => '#8b5cf6',
                'type' => 'special',
                'required_points' => 1000,
                'required_count' => 0,
                'requirement_type' => 'level',
                'rarity' => 2, // Rare
                'is_active' => true,
            ],
            [
                'name' => 'Level 20 Master',
                'slug' => 'level-20-badge',
                'description' => 'Reach level 20. You\'re a true master!',
                'icon' => 'fa-crown',
                'color' => '#f59e0b',
                'type' => 'special',
                'required_points' => 2000,
                'required_count' => 0,
                'requirement_type' => 'level',
                'rarity' => 3, // Epic
                'is_active' => true,
            ],
            [
                'name' => 'Level 30 Legend',
                'slug' => 'level-30-badge',
                'description' => 'Reach level 30. You\'re legendary!',
                'icon' => 'fa-gem',
                'color' => '#ec4899',
                'type' => 'special',
                'required_points' => 3000,
                'required_count' => 0,
                'requirement_type' => 'level',
                'rarity' => 3, // Epic
                'is_active' => true,
            ],
            [
                'name' => 'Level 50 God',
                'slug' => 'level-50-badge',
                'description' => 'Reach level 50. You\'ve achieved the impossible!',
                'icon' => 'fa-infinity',
                'color' => '#dc2626',
                'type' => 'special',
                'required_points' => 5000,
                'required_count' => 0,
                'requirement_type' => 'level',
                'rarity' => 4, // Legendary
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('badges')->insert([
                'name' => $badge['name'],
                'slug' => $badge['slug'],
                'description' => $badge['description'],
                'icon' => $badge['icon'],
                'color' => $badge['color'],
                'type' => $badge['type'],
                'required_points' => $badge['required_points'],
                'required_count' => $badge['required_count'],
                'requirement_type' => $badge['requirement_type'],
                'rarity' => $badge['rarity'],
                'is_active' => $badge['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Created ' . count($badges) . ' badges successfully!');
    }
}
