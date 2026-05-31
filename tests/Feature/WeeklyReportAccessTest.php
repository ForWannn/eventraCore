<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeeklyReport;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyReportAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'CEO']);
        Role::firstOrCreate(['name' => 'Employee']);
    }

    public function test_employee_can_access_weekly_history()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $response = $this->actingAs($employee)->get('/weekly-history');

        $response->assertStatus(200);
    }

    public function test_employee_can_access_own_weekly_report_detail()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $report = WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-05-25',
            'status' => 'submitted',
            'completion_percentage' => 80,
            'final_submitted_at' => now(),
        ]);

        $this->withoutExceptionHandling();
        $response = $this->actingAs($employee)->get("/weekly-recap/user/{$employee->id}/2026-05-25");

        $response->assertStatus(200);
    }

    public function test_employee_cannot_access_other_weekly_report_detail()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $other = User::factory()->create();
        $other->assignRole('Employee');

        $report = WeeklyReport::create([
            'user_id' => $other->id,
            'week_start_date' => '2026-05-25',
            'status' => 'submitted',
            'completion_percentage' => 80,
            'final_submitted_at' => now(),
        ]);

        $response = $this->actingAs($employee)->get("/weekly-recap/user/{$other->id}/2026-05-25");

        $response->assertStatus(403);
    }

    public function test_employee_history_with_search_and_filters()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        // Create reports
        WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-05-25',
            'status' => 'submitted',
            'completion_percentage' => 100,
            'is_late_plan' => false,
        ]);

        WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-06-01',
            'status' => 'draft',
            'completion_percentage' => 40,
            'is_late_plan' => true,
        ]);

        // Access history page with filters
        $response = $this->actingAs($employee)->get('/weekly-history?status=submitted&month=5&year=2026');

        $response->assertStatus(200);
        $response->assertViewHas('totalSubmitted', 1);
        $response->assertViewHas('totalOnTime', 1);
        $response->assertViewHas('totalLate', 0);
        $response->assertViewHas('averageCompletion', 100);
    }
}
