<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'Employee']);
        Role::firstOrCreate(['name' => 'Direktur']);

        // Create permissions
        Permission::firstOrCreate(['name' => 'leave_request']);
        Permission::firstOrCreate(['name' => 'leave_approvals']);
        Permission::firstOrCreate(['name' => 'view_dashboard']);
    }

    public function test_employee_can_filter_their_leave_history_by_date_range()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        // Create a leave request in May
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'izin',
            'start_date' => '2026-05-10',
            'end_date' => '2026-05-12',
            'reason' => 'Sakit',
            'status' => 'approved',
        ]);

        // Create a leave request in June
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-06-05',
            'end_date' => '2026-06-10',
            'reason' => 'Liburan',
            'status' => 'approved',
        ]);

        // Access without filters
        $response = $this->actingAs($employee)->get('/leave-requests');
        $response->assertStatus(200);
        $response->assertViewHas('requests', function ($requests) {
            return $requests->count() === 2;
        });

        // Filter for May
        $response = $this->actingAs($employee)->get('/leave-requests?filter_start_date=2026-05-01&filter_end_date=2026-05-31');
        $response->assertStatus(200);
        $response->assertViewHas('requests', function ($requests) {
            return $requests->count() === 1 && $requests->first()->reason === 'Sakit';
        });

        // Filter for June
        $response = $this->actingAs($employee)->get('/leave-requests?filter_start_date=2026-06-01&filter_end_date=2026-06-30');
        $response->assertStatus(200);
        $response->assertViewHas('requests', function ($requests) {
            return $requests->count() === 1 && $requests->first()->reason === 'Liburan';
        });
    }

    public function test_admin_can_filter_approvals_action_history_by_date_range()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Direktur');
        $admin->givePermissionTo('leave_approvals');

        $employee = User::factory()->create();

        // Create a leave request in May
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'izin',
            'start_date' => '2026-05-10',
            'end_date' => '2026-05-12',
            'reason' => 'Sakit',
            'status' => 'approved',
            'approved_by_id' => $admin->id,
        ]);

        // Create a leave request in June
        LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'cuti',
            'start_date' => '2026-06-05',
            'end_date' => '2026-06-10',
            'reason' => 'Liburan',
            'status' => 'rejected',
            'approved_by_id' => $admin->id,
        ]);

        // Access without filters
        $response = $this->actingAs($admin)->get('/leave-approvals');
        $response->assertStatus(200);
        $response->assertViewHas('historyRequests', function ($history) {
            return $history->count() === 2;
        });

        // Filter for May
        $response = $this->actingAs($admin)->get('/leave-approvals?filter_start_date=2026-05-01&filter_end_date=2026-05-31');
        $response->assertStatus(200);
        $response->assertViewHas('historyRequests', function ($history) {
            return $history->count() === 1 && $history->first()->reason === 'Sakit';
        });
    }
}
