<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DailyAttendance;
use App\Models\WorkCalendar;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Tests\TestCase;

class DailyAttendanceTimingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup roles
        Role::firstOrCreate(['name' => 'Employee']);
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Superadmin']);
        Role::firstOrCreate(['name' => 'Intern']);

        Storage::fake('public');
    }

    public function test_attendance_before_nine_zero_one_is_on_time()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        // Set a weekday (e.g., Monday 2026-06-15)
        $date = '2026-06-15';
        
        // 09:00:00 (On Time)
        Carbon::setTestNow(Carbon::parse("$date 09:00:00"));

        $response = $this->actingAs($employee)->postJson('/daily-attendance/store-luar', [
            'photo' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=', // Valid 1x1 base64 png
            'latitude' => -2.9507,
            'longitude' => 104.7454,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'tepat_waktu');

        $this->assertDatabaseHas('daily_attendances', [
            'user_id' => $employee->id,
            'date' => $date,
            'status' => 'tepat_waktu',
        ]);
    }

    public function test_attendance_exactly_at_nine_zero_zero_fifty_nine_is_on_time()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $date = '2026-06-16'; // Tuesday
        
        // 09:00:59 (On Time)
        Carbon::setTestNow(Carbon::parse("$date 09:00:59"));

        $response = $this->actingAs($employee)->postJson('/daily-attendance/store-luar', [
            'photo' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
            'latitude' => -2.9507,
            'longitude' => 104.7454,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'tepat_waktu');

        $this->assertDatabaseHas('daily_attendances', [
            'user_id' => $employee->id,
            'date' => $date,
            'status' => 'tepat_waktu',
        ]);
    }

    public function test_attendance_exactly_at_nine_zero_one_is_late()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $date = '2026-06-17'; // Wednesday
        
        // 09:01:00 (Late)
        Carbon::setTestNow(Carbon::parse("$date 09:01:00"));

        $response = $this->actingAs($employee)->postJson('/daily-attendance/store-luar', [
            'photo' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
            'latitude' => -2.9507,
            'longitude' => 104.7454,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'terlambat');

        $this->assertDatabaseHas('daily_attendances', [
            'user_id' => $employee->id,
            'date' => $date,
            'status' => 'terlambat',
        ]);
    }
}
