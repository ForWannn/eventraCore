<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeeklyReport;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyReportPdfExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'Direktur']);
        Role::firstOrCreate(['name' => 'Employee']);
    }

    public function test_employee_can_export_own_weekly_report_to_pdf()
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

        $response = $this->actingAs($employee)->get("/weekly-recap/user/{$employee->id}/2026-05-25/export-pdf");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_employee_cannot_export_other_weekly_report_to_pdf()
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

        $response = $this->actingAs($employee)->get("/weekly-recap/user/{$other->id}/2026-05-25/export-pdf");

        $response->assertStatus(403);
    }

    public function test_Direktur_can_export_any_employee_weekly_report_to_pdf()
    {
        $Direktur = User::factory()->create();
        $Direktur->assignRole('Direktur');

        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $report = WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-05-25',
            'status' => 'submitted',
            'completion_percentage' => 80,
            'final_submitted_at' => now(),
        ]);

        $response = $this->actingAs($Direktur)->get("/weekly-recap/user/{$employee->id}/2026-05-25/export-pdf");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
