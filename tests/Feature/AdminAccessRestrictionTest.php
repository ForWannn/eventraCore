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

    public function test_employee_with_weekly_history_permission_cannot_view_other_submitted_reports()
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

        // Employee with weekly_history permission can access weekly-history but should only see their own
        $response = $this->actingAs($employee)->get('/weekly-history');
        $response->assertStatus(200);
        $response->assertDontSee($otherEmployee->name);
    }

    public function test_Direktur_with_admin_role_can_access_employee_restricted_routes()
    {
        Role::firstOrCreate(['name' => 'Direktur']);
        
        $Direktur = User::factory()->create();
        $Direktur->assignRole('Direktur');
        $Direktur->assignRole('Admin');

        // Direktur should be able to access profile because they bypass the Admin restrictions
        $response = $this->actingAs($Direktur)->get('/profile');
        $response->assertStatus(200);
    }

    public function test_creating_new_user_assigns_default_permissions_based_on_role_and_division()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        // Create standard Division and a Finance Division
        $division = \App\Models\Division::create(['name' => 'Operasional']);
        $financeDivision = \App\Models\Division::create(['name' => 'Finance']);

        // Create role Intern if not exists
        Role::firstOrCreate(['name' => 'Intern']);

        // 1. Create Intern user
        $response = $this->actingAs($superadmin)->post('/users', [
            'name' => 'New Intern',
            'email' => 'intern@example.com',
            'nik' => 'INT-099',
            'password' => 'password123',
            'division_id' => $division->id,
            'role' => 'Intern',
            'join_date' => '2026-06-09',
            'employee_type' => 'Internship',
            'gender' => 'Laki-laki',
        ]);
        $response->assertRedirect('/users');
        $intern = User::where('nik', 'INT-099')->firstOrFail();
        $this->assertTrue($intern->hasRole('Intern'));
        $this->assertTrue($intern->hasDirectPermission('view_dashboard'));
        $this->assertTrue($intern->hasDirectPermission('weekly_report'));
        $this->assertTrue($intern->hasDirectPermission('weekly_history'));
        $this->assertTrue($intern->hasDirectPermission('leave_request'));
        $this->assertFalse($intern->hasDirectPermission('attendance_history'));
        $this->assertFalse($intern->hasDirectPermission('rekap_event'));

        // 2. Create Employee user in Finance Division
        $response = $this->actingAs($superadmin)->post('/users', [
            'name' => 'Finance Employee',
            'email' => 'finance@example.com',
            'nik' => 'EMP-099',
            'password' => 'password123',
            'division_id' => $financeDivision->id,
            'role' => 'Employee',
            'join_date' => '2026-06-09',
            'employee_type' => 'Full Time',
            'gender' => 'Perempuan',
        ]);
        $response->assertRedirect('/users');
        $financeEmp = User::where('nik', 'EMP-099')->firstOrFail();
        $this->assertTrue($financeEmp->hasRole('Employee'));
        $this->assertTrue($financeEmp->hasDirectPermission('view_dashboard'));
        $this->assertTrue($financeEmp->hasDirectPermission('weekly_report'));
        $this->assertTrue($financeEmp->hasDirectPermission('weekly_history'));
        $this->assertTrue($financeEmp->hasDirectPermission('leave_request'));
        $this->assertTrue($financeEmp->hasDirectPermission('attendance_history'));
        $this->assertTrue($financeEmp->hasDirectPermission('rekap_event')); // Finance gets rekap_event
    }

    public function test_updating_user_role_or_division_syncs_new_default_permissions()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        $division = \App\Models\Division::create(['name' => 'Operasional']);
        $financeDivision = \App\Models\Division::create(['name' => 'Finance']);

        Role::firstOrCreate(['name' => 'Intern']);

        // Create an Intern
        $user = User::factory()->create([
            'nik' => 'INT-101',
            'division_id' => $division->id,
            'employee_type' => 'Internship',
            'join_date' => '2026-06-09',
            'gender' => 'Laki-laki',
        ]);
        $user->assignRole('Intern');
        $user->syncPermissions(['view_dashboard', 'weekly_report', 'weekly_history', 'leave_request']);

        // Update user: change role to Employee, keep same division
        $response = $this->actingAs($superadmin)->put("/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'nik' => $user->nik,
            'division_id' => $division->id,
            'role' => 'Employee',
            'join_date' => '2026-06-09',
            'employee_type' => 'Full Time',
            'gender' => $user->gender,
        ]);
        $response->assertRedirect('/users');
        $user = $user->fresh();
        $this->assertTrue($user->hasRole('Employee'));
        $this->assertTrue($user->hasDirectPermission('attendance_history')); // Employee gets attendance_history

        // Manually customize permissions (e.g. check/uncheck something)
        $user->syncPermissions(['view_dashboard', 'weekly_report']);

        // Update user: change only name, keep same role and division
        $response = $this->actingAs($superadmin)->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
            'nik' => $user->nik,
            'division_id' => $division->id,
            'role' => 'Employee',
            'join_date' => '2026-06-09',
            'employee_type' => 'Full Time',
            'gender' => $user->gender,
        ]);
        $response->assertRedirect('/users');
        $user = $user->fresh();
        // Custom permissions should NOT be overwritten because role and division did not change
        $this->assertTrue($user->hasDirectPermission('view_dashboard'));
        $this->assertTrue($user->hasDirectPermission('weekly_report'));
        $this->assertFalse($user->hasDirectPermission('attendance_history')); // Custom remains customized
    }

    public function test_superadmin_cannot_access_restricted_modules_and_recap()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        // Blocked from executive-dashboard (Evaluasi Tahunan)
        $response = $this->actingAs($superadmin)->get('/executive-dashboard');
        $response->assertStatus(403);

        // Blocked from leave requests
        $response = $this->actingAs($superadmin)->get('/leave-requests');
        $response->assertStatus(403);

        // Blocked from leave approvals
        $response = $this->actingAs($superadmin)->get('/leave-approvals');
        $response->assertStatus(403);

        // Blocked from daily attendance recap
        $response = $this->actingAs($superadmin)->get('/daily-attendance-recap');
        $response->assertStatus(403);

        // Blocked from weekly recap
        $response = $this->actingAs($superadmin)->get('/weekly-recap');
        $response->assertStatus(403);

        // Blocked from attendance history
        $response = $this->actingAs($superadmin)->get('/attendance-history');
        $response->assertStatus(403);

        // Blocked from weekly history
        $response = $this->actingAs($superadmin)->get('/weekly-history');
        $response->assertStatus(403);
    }

    public function test_admin_with_crud_events_permission_can_manage_events()
    {
        Role::firstOrCreate(['name' => 'PIC Event']);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $admin->givePermissionTo(['crud_events']);

        $picUser = User::factory()->create();

        // 1. Test events create page access
        $response = $this->actingAs($admin)->get('/events/create');
        $response->assertStatus(200);

        // 2. Test events store
        $response = $this->actingAs($admin)->post('/events', [
            'name' => 'Test Event',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'event_dates' => '2026-06-20',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'attendance_start' => '07:30',
            'attendance_end' => '08:30',
            'pic_id' => $picUser->id,
        ]);
        $response->assertRedirect('/events');

        $event = \App\Models\Event::where('name', 'Test Event')->firstOrFail();

        // 3. Test edit page access
        $response = $this->actingAs($admin)->get("/events/{$event->id}/edit");
        $response->assertStatus(200);

        // 4. Test update
        $response = $this->actingAs($admin)->put("/events/{$event->id}", [
            'name' => 'Updated Event Name',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'event_dates' => '2026-06-20',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'attendance_start' => '07:30',
            'attendance_end' => '08:30',
            'pic_id' => $picUser->id,
        ]);
        $response->assertRedirect('/events');

        // 5. Test delete
        $response = $this->actingAs($admin)->delete("/events/{$event->id}");
        $response->assertRedirect('/events');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}
