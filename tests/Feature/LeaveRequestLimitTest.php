<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveRequestLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Roles and Permissions
        Role::firstOrCreate(['name' => 'Employee']);
        Role::firstOrCreate(['name' => 'Direktur']);
        Permission::firstOrCreate(['name' => 'leave_request']);
        Permission::firstOrCreate(['name' => 'leave_approvals']);
    }

    public function test_izin_requires_proof_document()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // Test sending izin without proof -> should fail validation
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'izin',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-11',
            'reason' => 'Sakit gigi',
        ]);

        $response->assertSessionHasErrors(['proof']);
        $this->assertDatabaseMissing('leave_requests', [
            'user_id' => $employee->id,
            'type' => 'izin',
        ]);
    }

    public function test_izin_with_proof_succeeds()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $proof = UploadedFile::fake()->create('surat_dokter.pdf', 500);

        // Test sending izin with proof -> should succeed
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'izin',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-11',
            'reason' => 'Sakit gigi',
            'proof' => $proof,
        ]);

        $response->assertRedirect('/leave-requests');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $employee->id,
            'type' => 'izin',
            'reason' => 'Sakit gigi',
        ]);

        // Verify the proof file is created in public folder (or fake it in tests)
        $leaveRequest = LeaveRequest::where('user_id', $employee->id)->first();
        $this->assertNotNull($leaveRequest->proof_path);
        $this->assertFileExists(public_path($leaveRequest->proof_path));

        // Clean up the uploaded file
        @unlink(public_path($leaveRequest->proof_path));
    }

    public function test_cuti_does_not_require_proof()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // Cuti <= 7 days without proof -> should succeed
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-06-08',
            'end_date' => '2026-06-12', // 5 weekdays
            'reason' => 'Liburan',
        ]);

        $response->assertRedirect('/leave-requests');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $employee->id,
            'type' => 'cuti',
            'reason' => 'Liburan',
            'proof_path' => null,
        ]);
    }

    public function test_cannot_request_cuti_more_than_7_days()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // Cuti 9 weekdays -> should fail limit validation (exceeds 7)
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-06-08',
            'end_date' => '2026-06-18', // 9 weekdays (excluding Sat/Sun)
            'reason' => 'Liburan panjang',
        ]);

        $response->assertSessionHasErrors(['end_date']);
        $this->assertDatabaseMissing('leave_requests', [
            'user_id' => $employee->id,
            'type' => 'cuti',
        ]);
    }

    public function test_cuti_limit_accrues_and_blocks_new_requests_exceeding_total()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // 1. Create already approved cuti of 5 days in 2026
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-05-11',
            'end_date' => '2026-05-15', // 5 weekdays (Mon-Fri)
            'reason' => 'Approved leave 1',
            'status' => 'approved',
        ]);

        // 2. Request a new cuti of 3 days in 2026 -> 5 + 3 = 8 > 7, should fail
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-08-03',
            'end_date' => '2026-08-05', // 3 weekdays (Mon-Wed)
            'reason' => 'Wants 3 more days',
        ]);

        $response->assertSessionHasErrors(['end_date']);
        $this->assertDatabaseMissing('leave_requests', [
            'user_id' => $employee->id,
            'type' => 'cuti',
            'reason' => 'Wants 3 more days',
        ]);

        // 3. Request a new cuti of 2 days in 2026 -> 5 + 2 = 7 <= 7, should succeed
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-08-03',
            'end_date' => '2026-08-04', // 2 weekdays (Mon-Tue)
            'reason' => 'Wants 2 more days',
        ]);

        $response->assertRedirect('/leave-requests');
        $response->assertSessionHas('success');
    }

    public function test_cuti_limit_is_reset_per_calendar_year()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // Create approved cuti of 6 weekdays in 2026
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-05-11',
            'end_date' => '2026-05-18', // 6 weekdays
            'reason' => 'Leave in 2026',
            'status' => 'approved',
        ]);

        // Request a new cuti of 5 weekdays in 2027 -> should succeed since it's a new calendar year
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2027-06-14',
            'end_date' => '2027-06-18', // 5 weekdays
            'reason' => 'Leave in 2027',
        ]);

        $response->assertRedirect('/leave-requests');
        $response->assertSessionHas('success');
    }

    public function test_Direktur_cannot_approve_cuti_exceeding_annual_limit()
    {
        $Direktur = User::factory()->create();
        $Direktur->assignRole('Direktur');
        $Direktur->givePermissionTo('leave_approvals');

        $employee = User::factory()->create();

        // 1. Create already approved cuti of 5 weekdays in 2026
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-05-11',
            'end_date' => '2026-05-15', // 5 weekdays
            'reason' => 'Approved leave 1',
            'status' => 'approved',
        ]);

        // 2. Create a pending cuti request of 3 weekdays
        $pending = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-08-03',
            'end_date' => '2026-08-05', // 3 weekdays
            'reason' => 'Wants 3 days',
            'status' => 'pending',
        ]);

        // 3. Direktur tries to approve the pending request -> should fail because it will exceed limit (5 + 3 = 8)
        $response = $this->actingAs($Direktur)->post("/leave-approvals/{$pending->id}/approve");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('pending', $pending->fresh()->status);
    }

    public function test_authorized_user_can_download_approved_cuti_pdf()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // Create approved cuti request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
            'reason' => 'Liburan',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($employee)->get("/leave-requests/{$leaveRequest->id}/download-pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_unauthorized_user_cannot_download_cuti_pdf()
    {
        $employee1 = User::factory()->create();
        $employee1->assignRole('Employee');
        $employee1->givePermissionTo('leave_request');

        $employee2 = User::factory()->create();
        $employee2->assignRole('Employee');
        $employee2->givePermissionTo('leave_request');

        // Create approved cuti request for employee1
        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee1->id,
            'type' => 'cuti',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
            'reason' => 'Liburan',
            'status' => 'approved',
        ]);

        // Attempt to download by employee2 -> should be 403
        $response = $this->actingAs($employee2)->get("/leave-requests/{$leaveRequest->id}/download-pdf");
        $response->assertStatus(403);
    }

    public function test_cannot_download_pending_cuti_pdf()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
            'reason' => 'Liburan',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employee)->get("/leave-requests/{$leaveRequest->id}/download-pdf");
        $response->assertStatus(400);
    }

    public function test_cannot_download_approved_izin_pdf()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'izin',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
            'reason' => 'Sakit',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($employee)->get("/leave-requests/{$leaveRequest->id}/download-pdf");
        $response->assertStatus(400);
    }

    public function test_cannot_request_cuti_on_weekend_only()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // Cuti on Sat 2026-06-13 and Sun 2026-06-14 -> should fail validation (0 weekdays)
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-06-13',
            'end_date' => '2026-06-14',
            'reason' => 'Liburan akhir pekan',
        ]);

        $response->assertSessionHasErrors(['end_date']);
        $this->assertDatabaseMissing('leave_requests', [
            'user_id' => $employee->id,
            'type' => 'cuti',
        ]);
    }
}

