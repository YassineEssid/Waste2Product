<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    /** @test */
    public function user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(strlen($user->password) > 20);
    }

    /** @test */
    public function user_has_default_points_on_creation()
    {
        $user = User::factory()->create();

        // New users should have no point transactions
        $this->assertEquals(0, $user->pointTransactions()->count());
    }

    /** @test */
    public function different_user_roles_can_be_created()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $repairer = User::factory()->create(['role' => 'repairer']);
        $artisan = User::factory()->create(['role' => 'artisan']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($repairer->isRepairer());
        $this->assertTrue($artisan->isArtisan());
        $this->assertFalse($user->isAdmin());
    }
}
