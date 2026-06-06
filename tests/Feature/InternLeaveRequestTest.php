<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class InternLeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'Intern']);
        Role::firstOrCreate(['name' => 'Employee']);

        // Create permissions
        Permission::firstOrCreate(['name' => 'leave_request']);
    }

    public function test_intern_can_request_izin_successfully()
    {
        $intern = User::factory()->create();
        $intern->assignRole('Intern');
        $intern->givePermissionTo('leave_request');

        $proof = UploadedFile::fake()->create('proof.pdf', 100);

        $response = $this->actingAs($intern)->post('/leave-requests', [
            'type' => 'izin',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-11',
            'reason' => 'Ada keperluan keluarga mendesak',
            'proof' => $proof,
        ]);

        $response->assertRedirect('/leave-requests');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $intern->id,
            'type' => 'izin',
            'reason' => 'Ada keperluan keluarga mendesak',
        ]);

        $leaveRequest = LeaveRequest::where('user_id', $intern->id)->first();
        if ($leaveRequest && $leaveRequest->proof_path) {
            @unlink(public_path($leaveRequest->proof_path));
        }
    }

    public function test_intern_cannot_request_cuti()
    {
        $intern = User::factory()->create();
        $intern->assignRole('Intern');
        $intern->givePermissionTo('leave_request');

        $response = $this->actingAs($intern)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-15',
            'reason' => 'Liburan akhir tahun',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['type']);

        $this->assertDatabaseMissing('leave_requests', [
            'user_id' => $intern->id,
            'type' => 'cuti',
        ]);
    }

    public function test_regular_employee_can_request_both_izin_and_cuti()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $proof = UploadedFile::fake()->create('proof.pdf', 100);

        // Test Employee requesting Izin
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'izin',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-11',
            'reason' => 'Sakit kepala',
            'proof' => $proof,
        ]);
        $response->assertRedirect('/leave-requests');
        $response->assertSessionHas('success');

        $leaveRequest = LeaveRequest::where('user_id', $employee->id)->where('type', 'izin')->first();
        if ($leaveRequest && $leaveRequest->proof_path) {
            @unlink(public_path($leaveRequest->proof_path));
        }

        // Test Employee requesting Cuti
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-20',
            'reason' => 'Liburan keluarga',
        ]);
        $response->assertRedirect('/leave-requests');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $employee->id,
            'type' => 'cuti',
            'reason' => 'Liburan keluarga',
        ]);
    }
}
