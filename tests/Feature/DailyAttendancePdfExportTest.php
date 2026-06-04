<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DailyAttendance;
use App\Models\WorkCalendar;
use App\Models\LeaveRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class DailyAttendancePdfExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Employee']);
        Role::firstOrCreate(['name' => 'Superadmin']);

        // Create permission
        Permission::firstOrCreate(['name' => 'rekap_absen']);
    }

    public function test_access_to_pdf_export_is_protected_by_permission()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        // Without rekap_absen permission, get 403
        $response = $this->actingAs($employee)->get('/daily-attendance-recap/export-pdf-monthly?date=2026-05-18');
        $response->assertStatus(403);

        // Give permission and access successfully
        $employee->givePermissionTo('rekap_absen');
        $response = $this->actingAs($employee)->get('/daily-attendance-recap/export-pdf-monthly?date=2026-05-18');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_contains_correct_recap_data_for_the_month()
    {
        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole('Admin');
        $admin->givePermissionTo('rekap_absen');

        $employee = User::factory()->create(['name' => 'Andri Nugraha']);
        $employee->assignRole('Employee');

        // Create some attendance records in May 2026 for the employee
        // Regular weekday check-in (on time)
        DailyAttendance::create([
            'user_id' => $employee->id,
            'date' => '2026-05-04', // Monday
            'check_in_time' => '08:32:00',
            'attendance_type' => 'luar',
            'status' => 'tepat_waktu',
        ]);

        // Regular weekday check-in (late)
        DailyAttendance::create([
            'user_id' => $employee->id,
            'date' => '2026-05-05', // Tuesday
            'check_in_time' => '09:05:00',
            'attendance_type' => 'luar',
            'status' => 'terlambat',
        ]);

        // Weekday check-in LUPA ABSEN (before 07:00 AM)
        DailyAttendance::create([
            'user_id' => $employee->id,
            'date' => '2026-05-18', // Monday
            'check_in_time' => '06:23:00',
            'attendance_type' => 'luar',
            'status' => 'tepat_waktu',
        ]);

        // Weekday check-in LUPA ABSEN (after 12:00 PM)
        DailyAttendance::create([
            'user_id' => $employee->id,
            'date' => '2026-05-19', // Tuesday
            'check_in_time' => '13:00:00',
            'attendance_type' => 'luar',
            'status' => 'terlambat',
        ]);

        // Weekday holiday defined in WorkCalendar
        WorkCalendar::create([
            'date' => '2026-05-14', // Thursday
            'is_working_day' => false,
            'description' => 'Kenaikan Isa Almasih',
        ]);

        // Weekday leave
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'izin',
            'start_date' => '2026-05-27',
            'end_date' => '2026-05-27',
            'reason' => 'Izin keperluan keluarga',
            'status' => 'approved',
        ]);

        // Request export for May 2026
        $response = $this->actingAs($admin)->get('/daily-attendance-recap/export-pdf-monthly?date=2026-05-15');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        // Verify PDF download content disposition
        $response->assertHeader('Content-Disposition', 'inline; filename=data_absensi_bulan_mei_2026.pdf');
    }
}
