<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventComment;
use App\Models\CommunityEvent;
use App\Models\User;

class EventCommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all events and users
        $events = CommunityEvent::all();
        $users = User::all();

        if ($events->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No events or users found. Please seed events and users first.');
            return;
        }

        $comments = [
            [
                'comment' => 'This was an amazing event! I learned so much about sustainable living and met wonderful people who share the same passion for the environment.',
                'rating' => 5,
            ],
            [
                'comment' => 'Great initiative! The workshop was very informative and the organizers did an excellent job. Looking forward to more events like this.',
                'rating' => 5,
            ],
            [
                'comment' => 'Good event overall, but I think it could have been better organized. The content was valuable though.',
                'rating' => 4,
            ],
            [
                'comment' => 'I really enjoyed the hands-on activities. It was practical and engaging. The venue was perfect too!',
                'rating' => 5,
            ],
            [
                'comment' => 'The event was okay. Some parts were interesting, but it felt a bit rushed. Could use more time for Q&A sessions.',
                'rating' => 3,
            ],
            [
                'comment' => 'Excellent presentation and very knowledgeable speakers. I appreciated the focus on actionable steps we can take.',
                'rating' => 5,
            ],
            [
                'comment' => 'Not bad, but the event could have been more interactive. Still, I learned a few useful tips about waste reduction.',
                'rating' => 3,
            ],
            [
                'comment' => 'Fantastic event! The community spirit was incredible. Everyone was so friendly and willing to share their experiences.',
                'rating' => 5,
            ],
            [
                'comment' => 'I was impressed by the variety of topics covered. From composting to upcycling, there was something for everyone.',
                'rating' => 4,
            ],
            [
                'comment' => 'The event exceeded my expectations. The activities were well-planned and the materials provided were very helpful.',
                'rating' => 5,
            ],
            [
                'comment' => 'Good content but the timing could be better. It was hard to attend during working hours.',
                'rating' => 3,
            ],
            [
                'comment' => 'I loved the collaborative atmosphere! Everyone was eager to learn and share ideas about sustainability.',
                'rating' => 5,
            ],
            [
                'comment' => 'The event was educational and fun. I particularly enjoyed the recycling demonstration.',
                'rating' => 4,
            ],
            [
                'comment' => 'Well organized and informative. The speakers were passionate and knowledgeable about their topics.',
                'rating' => 5,
            ],
            [
                'comment' => 'Decent event. Would have liked more practical examples and less theory.',
                'rating' => 3,
            ],
        ];

        // Create comments for random events and users
        foreach ($comments as $commentData) {
            EventComment::create([
                'community_event_id' => $events->random()->id,
                'user_id' => $users->random()->id,
                'comment' => $commentData['comment'],
                'rating' => $commentData['rating'],
                'is_approved' => true,
                'commented_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('Event comments seeded successfully!');
    }
}

