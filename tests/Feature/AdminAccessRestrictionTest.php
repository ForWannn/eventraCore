<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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
        Role::firstOrCreate(['name' => 'Superadmin']);
        Role::firstOrCreate(['name' => 'Employee']);

        // Create permissions
        $availablePermissions = ['crud_users', 'crud_events', 'manage_calendar', 'rekap_absen', 'rekap_weekly', 'weekly_history', 'leave_approvals', 'view_dashboard', 'weekly_report', 'leave_request', 'attendance_history', 'rekap_event'];
        foreach ($availablePermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }

    public function test_admin_with_permissions_can_access_allowed_routes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $admin->givePermissionTo(['crud_users', 'manage_calendar', 'view_dashboard']);

        // Test dashboard access
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        // Test users index access
        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);

        // Test calendar access
        $response = $this->actingAs($admin)->get('/settings/calendar');
        $response->assertStatus(200);

        // Test events index access (public route)
        $response = $this->actingAs($admin)->get('/events');
        $response->assertStatus(200);
    }

    public function test_admin_without_permissions_is_restricted()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Dashboard is blocked because admin does not have view_dashboard permission
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(403);

        // Blocked from users index because no crud_users permission
        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(403);

        // Blocked from calendar because no manage_calendar permission
        $response = $this->actingAs($admin)->get('/settings/calendar');
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_employee_restricted_routes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Admin should be blocked from profile page
        $response = $this->actingAs($admin)->get('/profile');
        $response->assertStatus(403);

        // Admin should be blocked from weekly report page
        $response = $this->actingAs($admin)->get('/weekly-report');
        $response->assertStatus(403);
    }

    public function test_non_admin_is_not_blocked_from_normal_routes()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo(['view_dashboard', 'weekly_report', 'leave_request']);

        // Employee should access profile fine
        $response = $this->actingAs($employee)->get('/profile');
        $response->assertStatus(200);

        // Employee should access weekly report fine
        $response = $this->actingAs($employee)->get('/weekly-report');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_everything()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        // Superadmin bypasses Gate checks and has access to all routes
        $response = $this->actingAs($superadmin)->get('/users');
        $response->assertStatus(200);

        $response = $this->actingAs($superadmin)->get('/settings/calendar');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_permissions_editor_and_bulk_update_permissions()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        // Superadmin can load the view
        $response = $this->actingAs($superadmin)->get('/settings/permissions');
        $response->assertStatus(200);
        $response->assertSee($employee->name);

        // Superadmin can bulk update permissions
        $response = $this->actingAs($superadmin)->post('/settings/permissions', [
            'permissions' => [
                $employee->id => [
                    'crud_users' => '1',
                    'crud_events' => '1',
                ]
            ]
        ]);
        $response->assertRedirect();

        $this->assertTrue($employee->hasDirectPermission('crud_users'));
        $this->assertTrue($employee->hasDirectPermission('crud_events'));
        $this->assertFalse($employee->hasDirectPermission('manage_calendar'));
    }

    public function test_non_superadmin_cannot_access_permissions_editor()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get('/settings/permissions');
        $response->assertStatus(403);

        $response = $this->actingAs($admin)->post('/settings/permissions', [
            'permissions' => []
        ]);
        $response->assertStatus(403);
    }

    public function test_superadmin_cannot_revoke_own_permissions()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');
        $superadmin->givePermissionTo('crud_users');

        $response = $this->actingAs($superadmin)->post('/settings/permissions', [
            'permissions' => [
                $superadmin->id => [
                    // Empty list implies revoking all permissions, but lockout safeguard should protect it
                ]
            ]
        ]);
        $response->assertRedirect();

        // Fresh instance from DB
        $superadmin = $superadmin->fresh();
        $this->assertTrue($superadmin->hasDirectPermission('crud_users'));
    }

    public function test_user_permission_guards_for_dashboard_weekly_report_and_leave_requests()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        // Without permissions, employee is blocked from dashboard, weekly report, and leave requests
        $response = $this->actingAs($employee)->get('/dashboard');
        $response->assertStatus(403);

        $response = $this->actingAs($employee)->get('/weekly-report');
        $response->assertStatus(403);

        $response = $this->actingAs($employee)->get('/leave-requests');
        $response->assertStatus(403);

        // Give permissions one by one and verify access
        $employee->givePermissionTo('view_dashboard');
        $response = $this->actingAs($employee)->get('/dashboard');
        $response->assertStatus(200);

        $employee->givePermissionTo('weekly_report');
        $response = $this->actingAs($employee)->get('/weekly-report');
        $response->assertStatus(200);

        $employee->givePermissionTo('leave_request');
        $response = $this->actingAs($employee)->get('/leave-requests');
        $response->assertStatus(200);
    }

    public function test_user_permission_guards_for_attendance_history_and_weekly_history()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        // Without permissions, employee is blocked from attendance history
        $response = $this->actingAs($employee)->get('/attendance-history');
        $response->assertStatus(403);

        // Give attendance_history permission and verify access
        $employee->givePermissionTo('attendance_history');
        $response = $this->actingAs($employee)->get('/attendance-history');
        $response->assertStatus(200);
    }

    public function test_employee_with_weekly_history_permission_can_view_all_submitted_reports()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('weekly_history');

        $otherEmployee = User::factory()->create();
        $otherEmployee->assignRole('Employee');

        // Create a submitted weekly report for the other employee
        \App\Models\WeeklyReport::create([
            'user_id' => $otherEmployee->id,
            'week_start_date' => '2026-05-25',
            'status' => 'submitted',
            'completion_percentage' => 90,
            'final_submitted_at' => now(),
        ]);

        // Employee with weekly_history permission can access weekly-history and should see other's report
        $response = $this->actingAs($employee)->get('/weekly-history');
        $response->assertStatus(200);
        $response->assertSee($otherEmployee->name);
    }
}
