<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KitchenAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_chef_can_access_the_kitchen_panel(): void
    {
        $chef = User::factory()->create([
            'name' => 'Chef User',
            'email' => 'chef@example.com',
            'password' => Hash::make('secret-password'),
            'role' => 'chef',
            'is_active' => true,
        ]);

        $this->actingAs($chef)
            ->get('/kitchen')
            ->assertOk();
    }

    public function test_manager_cannot_access_the_kitchen_panel_anymore(): void
    {
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('secret-password'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->actingAs($manager)
            ->get('/kitchen')
            ->assertForbidden();
    }
}
