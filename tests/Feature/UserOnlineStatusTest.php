<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserOnlineStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_middleware_records_user_activity_and_sets_cache()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $this->assertNull($user->last_seen_at);
        $this->assertFalse(Cache::has('user-is-online-' . $user->id));

        // Perform request to trigger the UpdateUserOnlineStatus middleware
        $response = $this->actingAs($user)->get('/dashboard');

        $user->refresh();
        $this->assertNotNull($user->last_seen_at);
        $this->assertTrue(Cache::has('user-is-online-' . $user->id));
        $this->assertTrue($user->isOnline());
    }

    public function test_superadmin_can_see_online_users_in_permissions_panel()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        $employee = User::factory()->create(['name' => 'Active Worker']);
        $employee->assignRole('Employee');

        // Mock employee as online
        Cache::put('user-is-online-' . $employee->id, true, now()->addMinutes(5));
        $employee->update(['last_seen_at' => now()->subMinutes(2)]);

        $response = $this->actingAs($superadmin)->get('/settings/permissions');

        $response->assertStatus(200);
        $response->assertSee('Active Worker');
        $response->assertSee('(Online)');
        $response->assertSee('Pengguna Online Saat Ini');
    }
}
