<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required system roles
        Role::firstOrCreate(['name' => 'Intern']);
        Role::firstOrCreate(['name' => 'Employee']);

        // Create required system permissions
        $permissions = ['view_dashboard', 'attendance_history'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }

    public function test_intern_dashboard_does_not_contain_attendance_sections()
    {
        $intern = User::factory()->create();
        $intern->assignRole('Intern');
        $intern->givePermissionTo('view_dashboard');

        $response = $this->actingAs($intern)->get('/dashboard');

        $response->assertStatus(200);

        // Verify that daily attendance, monthly attendance, and recent history cards are hidden
        $response->assertDontSee('Absensi Hari Ini');
        $response->assertDontSee('Absensi Bulan Ini');
        $response->assertDontSee('Riwayat Absen Terbaru');
        
        // Intern dashboard stats grid should have 2 columns
        $response->assertSee('style="grid-template-columns: repeat(2, 1fr);"', false);
    }

    public function test_normal_employee_dashboard_contains_attendance_sections()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('view_dashboard');

        $response = $this->actingAs($employee)->get('/dashboard');

        $response->assertStatus(200);

        // Verify that daily attendance, monthly attendance, and recent history cards are visible
        $response->assertSee('Absensi Hari Ini');
        $response->assertSee('Absensi Bulan Ini');
        $response->assertSee('Riwayat Absen Terbaru');

        // Normal dashboard stats grid should not override grid-template-columns to 2 columns inline
        $response->assertDontSee('style="grid-template-columns: repeat(2, 1fr);"', false);
    }

    public function test_intern_is_blocked_from_viewing_attendance_history()
    {
        $intern = User::factory()->create();
        $intern->assignRole('Intern');
        $intern->givePermissionTo(['view_dashboard', 'attendance_history']);

        // Directly accessing the history route should return 403 Forbidden
        $response = $this->actingAs($intern)->get('/attendance-history');
        $response->assertStatus(403);
    }

    public function test_intern_is_blocked_from_submitting_attendance()
    {
        $intern = User::factory()->create();
        $intern->assignRole('Intern');

        // Submitting attendance should return 403 Forbidden
        $response = $this->actingAs($intern)->postJson('/daily-attendance/store-luar', [
            'photo' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'latitude' => '-2.9507',
            'longitude' => '104.7454',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Intern tidak diperbolehkan melakukan absensi.'
        ]);
    }

    public function test_admin_is_blocked_from_submitting_attendance_and_history()
    {
        Role::firstOrCreate(['name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Submitting attendance should return 403 Forbidden
        $response = $this->actingAs($admin)->postJson('/daily-attendance/store-luar', [
            'photo' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'latitude' => '-2.9507',
            'longitude' => '104.7454',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Akses ditolak untuk Admin.'
        ]);

        // Accessing history should return 403
        $response = $this->actingAs($admin)->get('/attendance-history');
        $response->assertStatus(403);
    }

    public function test_superadmin_is_blocked_from_submitting_attendance_and_history()
    {
        Role::firstOrCreate(['name' => 'Superadmin']);
        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        // Submitting attendance should return 403 Forbidden
        $response = $this->actingAs($superadmin)->postJson('/daily-attendance/store-luar', [
            'photo' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'latitude' => '-2.9507',
            'longitude' => '104.7454',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Superadmin tidak diperbolehkan melakukan absensi.'
        ]);

        // Accessing history should return 403
        $response = $this->actingAs($superadmin)->get('/attendance-history');
        $response->assertStatus(403);
    }

    public function test_recap_page_excludes_admin_superadmin_and_intern_from_list_and_statistics()
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Superadmin']);
        Role::firstOrCreate(['name' => 'GM']);
        Permission::firstOrCreate(['name' => 'rekap_absen']);

        $gm = User::factory()->create();
        $gm->assignRole('GM');
        $gm->givePermissionTo('rekap_absen');

        $adminUser = User::factory()->create(['name' => 'Admin User Name']);
        $adminUser->assignRole('Admin');

        $superadminUser = User::factory()->create(['name' => 'Superadmin User Name']);
        $superadminUser->assignRole('Superadmin');

        $internUser = User::factory()->create(['name' => 'Intern User Name']);
        $internUser->assignRole('Intern');

        $employeeUser = User::factory()->create(['name' => 'Employee User Name']);
        $employeeUser->assignRole('Employee');

        $response = $this->actingAs($gm)->get('/daily-attendance-recap');

        $response->assertStatus(200);

        // Verify employee is seen, but others are not
        $response->assertSee('Employee User Name');
        $response->assertDontSee('Admin User Name');
        $response->assertDontSee('Superadmin User Name');
        $response->assertDontSee('Intern User Name');

        // Total staff count on the card should be 2 (employeeUser and gm)
        $response->assertViewHas('totalStaff', 2);
    }
}
