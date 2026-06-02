<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessRestrictionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Employee']);
    }

    public function test_admin_can_access_allowed_routes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Test dashboard access
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        // Test users index access
        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);

        // Test calendar access
        $response = $this->actingAs($admin)->get('/settings/calendar');
        $response->assertStatus(200);

        // Test events index access
        $response = $this->actingAs($admin)->get('/events');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_restricted_routes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Admin should be blocked from profile page
        $response = $this->actingAs($admin)->get('/profile');
        $response->assertStatus(403);

        // Admin should be blocked from weekly report page
        $response = $this->actingAs($admin)->get('/weekly-report');
        $response->assertStatus(403);

        // Admin should be blocked from leave requests index page
        $response = $this->actingAs($admin)->get('/leave-requests');
        $response->assertStatus(403);

        // Admin should be blocked from attendance history page
        $response = $this->actingAs($admin)->get('/attendance-history');
        $response->assertStatus(403);
    }

    public function test_non_admin_is_not_blocked_from_normal_routes()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        // Employee should access profile fine
        $response = $this->actingAs($employee)->get('/profile');
        $response->assertStatus(200);

        // Employee should access weekly report fine
        $response = $this->actingAs($employee)->get('/weekly-report');
        $response->assertStatus(200);

        // Employee should access leave requests fine
        $response = $this->actingAs($employee)->get('/leave-requests');
        $response->assertStatus(200);

        // Employee should access attendance history fine
        $response = $this->actingAs($employee)->get('/attendance-history');
        $response->assertStatus(200);
    }
}
