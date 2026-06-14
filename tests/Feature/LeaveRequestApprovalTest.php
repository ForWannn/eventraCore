<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LeaveRequestApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Employee']);
        Role::firstOrCreate(['name' => 'GM']);
        Role::firstOrCreate(['name' => 'CEO']);
        Role::firstOrCreate(['name' => 'Superadmin']);

        // Create permissions
        Permission::firstOrCreate(['name' => 'leave_request']);
        Permission::firstOrCreate(['name' => 'leave_approvals']);
        Permission::firstOrCreate(['name' => 'rekap_absen']);
    }

    public function test_izin_approval_by_gm_only_is_immediately_approved()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $gm = User::factory()->create();
        $gm->assignRole('GM');
        $gm->givePermissionTo('leave_approvals');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'izin',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-15',
            'reason' => 'Izin sakit gigi',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($gm)->post("/leave-approvals/{$leaveRequest->id}/approve");

        $response->assertRedirect();
        $this->assertEquals('approved', $leaveRequest->fresh()->status);
        $this->assertEquals($gm->id, $leaveRequest->fresh()->approved_by_id);
    }

    public function test_izin_approval_by_ceo_only_is_immediately_approved()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $ceo = User::factory()->create();
        $ceo->assignRole('CEO');
        $ceo->givePermissionTo('leave_approvals');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'izin',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-15',
            'reason' => 'Izin sakit gigi',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($ceo)->post("/leave-approvals/{$leaveRequest->id}/approve");

        $response->assertRedirect();
        $this->assertEquals('approved', $leaveRequest->fresh()->status);
        $this->assertEquals($ceo->id, $leaveRequest->fresh()->approved_by_id);
    }

    public function test_cuti_approval_requires_both_gm_and_ceo()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $gm = User::factory()->create();
        $gm->assignRole('GM');
        $gm->givePermissionTo('leave_approvals');

        $ceo = User::factory()->create();
        $ceo->assignRole('CEO');
        $ceo->givePermissionTo('leave_approvals');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-16', // 2 days
            'reason' => 'Cuti tahunan',
            'status' => 'pending',
        ]);

        // 1. GM Approves
        $response1 = $this->actingAs($gm)->post("/leave-approvals/{$leaveRequest->id}/approve");
        $response1->assertRedirect();
        
        $leaveRequest = $leaveRequest->fresh();
        $this->assertEquals('pending', $leaveRequest->status); // Still pending
        $this->assertEquals($gm->id, $leaveRequest->approved_by_gm_id);
        $this->assertNull($leaveRequest->approved_by_ceo_id);

        // 2. Try to download PDF (should fail with 400 since not fully approved yet)
        $pdfResponse1 = $this->actingAs($employee)->get("/leave-requests/{$leaveRequest->id}/download-pdf");
        $pdfResponse1->assertStatus(400);

        // 3. CEO Approves
        $response2 = $this->actingAs($ceo)->post("/leave-approvals/{$leaveRequest->id}/approve");
        $response2->assertRedirect();

        $leaveRequest = $leaveRequest->fresh();
        $this->assertEquals('approved', $leaveRequest->status); // Now approved!
        $this->assertEquals($gm->id, $leaveRequest->approved_by_gm_id);
        $this->assertEquals($ceo->id, $leaveRequest->approved_by_ceo_id);
        $this->assertEquals($ceo->id, $leaveRequest->approved_by_id); // final approved_by_id is CEO

        // 4. Download PDF (should succeed with 200)
        $pdfResponse2 = $this->actingAs($employee)->get("/leave-requests/{$leaveRequest->id}/download-pdf");
        $pdfResponse2->assertStatus(200);
        $pdfResponse2->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_cuti_rejection_by_either_gm_or_ceo_is_immediate()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $gm = User::factory()->create();
        $gm->assignRole('GM');
        $gm->givePermissionTo('leave_approvals');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-16',
            'reason' => 'Cuti tahunan',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($gm)->post("/leave-approvals/{$leaveRequest->id}/reject");
        $response->assertRedirect();

        $this->assertEquals('rejected', $leaveRequest->fresh()->status);
    }
}
