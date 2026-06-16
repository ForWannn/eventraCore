<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeeklyReport;
use App\Models\WeeklyItem;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyReportThreeButtonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Direktur']);
        Role::firstOrCreate(['name' => 'Employee']);

        Permission::firstOrCreate(['name' => 'weekly_report']);
        Permission::firstOrCreate(['name' => 'rekap_weekly']);
        Permission::firstOrCreate(['name' => 'weekly_history']);
    }

    public function test_save_draft_updates_plan_saved_at_only_on_changes()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('weekly_report');

        $report = WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-06-15',
            'status' => 'draft',
        ]);

        // 1. Save with new objectives (changes)
        $response = $this->actingAs($employee)->post(route('weekly.plan', $report->id), [
            'objectives' => ['Objective 1', 'Objective 2'],
            'deadlines' => ['Deadline 1']
        ]);

        $response->assertSessionHas('success');
        $report->refresh();
        $this->assertNotNull($report->plan_saved_at);
        $savedAt1 = $report->plan_saved_at;

        $this->assertEquals(2, $report->items()->where('type', 'objective')->count());
        $this->assertEquals(1, $report->items()->where('type', 'deadline')->count());

        // Perform another post with identical data
        $response2 = $this->actingAs($employee)->post(route('weekly.plan', $report->id), [
            'objectives' => ['Objective 1', 'Objective 2'],
            'deadlines' => ['Deadline 1']
        ]);

        $response2->assertSessionHas('info', 'Tidak ada perubahan pada Weekly Plan.');
        $report->refresh();
        $this->assertEquals($savedAt1->toDateTimeString(), $report->plan_saved_at->toDateTimeString());

        // Post with changes
        $this->travel(5)->minutes();

        $response3 = $this->actingAs($employee)->post(route('weekly.plan', $report->id), [
            'objectives' => ['Objective 1', 'Objective 2 Modified'],
            'deadlines' => ['Deadline 1']
        ]);

        $response3->assertSessionHas('success');
        $report->refresh();
        $this->assertNotEquals($savedAt1->toDateTimeString(), $report->plan_saved_at->toDateTimeString());
    }

    public function test_cannot_save_draft_when_locked()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('weekly_report');

        $report = WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-06-15',
            'status' => 'draft',
            'plan_submitted_at' => now(),
        ]);

        $response = $this->actingAs($employee)->post(route('weekly.plan', $report->id), [
            'objectives' => ['Objective 1'],
            'deadlines' => []
        ]);

        $response->assertSessionHas('error', 'Weekly Plan sudah dikirim dan tidak dapat diubah.');
    }

    public function test_submit_plan_locks_inputs_and_registers_submission()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('weekly_report');

        $report = WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-06-15',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($employee)->post(route('weekly.submit_plan', $report->id), [
            'objectives' => ['Objective A'],
            'deadlines' => ['Deadline A']
        ]);

        $response->assertSessionHas('success');
        $report->refresh();
        $this->assertNotNull($report->plan_submitted_at);
        $this->assertNotNull($report->plan_saved_at);
        $this->assertEquals(1, $report->items()->where('type', 'objective')->count());
    }

    public function test_unsubmitted_plan_details_are_hidden_from_others()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('weekly_report');

        $direktur = User::factory()->create();
        $direktur->assignRole('Direktur');

        $report = WeeklyReport::create([
            'user_id' => $employee->id,
            'week_start_date' => '2026-06-15',
            'status' => 'draft',
            'plan_saved_at' => now(),
        ]);

        WeeklyItem::create(['weekly_report_id' => $report->id, 'type' => 'objective', 'content' => 'Secret Objective']);
        WeeklyItem::create(['weekly_report_id' => $report->id, 'type' => 'deadline', 'content' => 'Secret Deadline']);

        // 1. Owner can see details
        $this->actingAs($employee)->get("/weekly-recap/user/{$employee->id}/2026-06-15")
            ->assertStatus(200)
            ->assertSee('Secret Objective')
            ->assertSee('Secret Deadline')
            ->assertDontSee('Weekly Plan belum dikirim oleh karyawan.');

        // 2. Direktur cannot see details (gets placeholder)
        $this->actingAs($direktur)->get("/weekly-recap/user/{$employee->id}/2026-06-15")
            ->assertStatus(200)
            ->assertDontSee('Secret Objective')
            ->assertDontSee('Secret Deadline')
            ->assertSee('Weekly Plan belum dikirim oleh karyawan.');

        // 3. Export PDF also hides it
        $response = $this->actingAs($direktur)->get("/weekly-recap/user/{$employee->id}/2026-06-15/export-pdf");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        // Test the PDF view rendering directly
        $pdfView = $this->view('weekly-reports.pdf', [
            'report' => $report,
            'user' => $employee,
            'dateRangeString' => '15 Juni 2026 - 19 Juni 2026',
            'canViewPlan' => false,
        ]);
        $pdfView->assertSee('Weekly Plan belum dikirim.');
        $pdfView->assertDontSee('Secret Objective');

        // 4. Now submit plan
        $report->update(['plan_submitted_at' => now()]);

        // 5. Direktur now can see details
        $this->actingAs($direktur)->get("/weekly-recap/user/{$employee->id}/2026-06-15")
            ->assertStatus(200)
            ->assertSee('Secret Objective')
            ->assertSee('Secret Deadline')
            ->assertDontSee('Weekly Plan belum dikirim oleh karyawan.');
    }
}
