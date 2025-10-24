<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WasteItem;
use App\Models\User;

class WasteItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (or create one if doesn't exist)
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $wasteItems = [
            [
                'user_id' => $user->id,
                'title' => 'Old Wooden Pallet',
                'description' => 'Sturdy wooden pallet in good condition. Perfect for DIY furniture projects, garden planters, or wall decorations. Dimensions: 120x80cm. Can be easily disassembled or used as is.',
                'category' => 'wood',
                'condition' => 'good',
                'quantity' => 3,
                'unit' => 'pieces',
                'location' => 'Centre ville',
                'is_available' => true,
                'image' => null,
            ],
            [
                'user_id' => $user->id,
                'title' => 'Plastic Bottles Collection',
                'description' => 'Collection of clean plastic bottles (PET). Various sizes from 0.5L to 2L. Great for craft projects, vertical gardens, or recycling into useful household items. Already cleaned and ready to use.',
                'category' => 'plastic',
                'condition' => 'excellent',
                'quantity' => 50,
                'unit' => 'pieces',
                'location' => 'Mourouj 1',
                'is_available' => true,
                'image' => null,
            ],
            [
                'user_id' => $user->id,
                'title' => 'Broken Electronics - Circuit Boards',
                'description' => 'Collection of old circuit boards from computers, printers, and other electronics. Components can be salvaged for DIY electronics projects. Contains various resistors, capacitors, and connectors. Ideal for learning electronics repair or creating art.',
                'category' => 'electronics',
                'condition' => 'poor',
                'quantity' => 10,
                'unit' => 'kg',
                'location' => 'Centre ville',
                'is_available' => true,
                'image' => null,
            ],
        ];

        foreach ($wasteItems as $item) {
            WasteItem::create($item);
        }

        $this->command->info('✅ 3 waste items created successfully!');
    }
}
