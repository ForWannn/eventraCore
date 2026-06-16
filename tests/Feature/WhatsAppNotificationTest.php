<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\EventTask;
use App\Models\LeaveRequest;
use App\Models\WeeklyReport;
use App\Models\WorkCalendar;
use App\Models\DailyAttendance;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Tests\TestCase;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $roles = ['Direktur', 'GM', 'Head', 'PIC Event', 'Employee', 'Intern', 'Admin', 'Superadmin'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        $permissions = [
            'crud_users',
            'crud_events',
            'rekap_absen',
            'rekap_weekly',
            'weekly_history',
            'manage_calendar',
            'leave_approvals',
            'view_dashboard',
            'weekly_report',
            'leave_request',
            'attendance_history',
            'rekap_event'
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Set Fonnte config token
        config(['services.fonnte.token' => 'test-fonnte-token']);
    }

    public function test_fonnte_service_formats_numbers_and_sends_post_request()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $result = \App\Services\FonnteService::send('08123456789, +628111222333', 'Test message');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send' &&
                $request['target'] === '628123456789,628111222333' &&
                $request['message'] === 'Test message' &&
                $request->hasHeader('Authorization', 'test-fonnte-token');
        });
    }

    public function test_event_creation_triggers_notifications_to_pic_and_crew()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $gm = User::factory()->create();
        $gm->assignRole('GM');
        $gm->givePermissionTo('crud_events');

        $pic = User::factory()->create(['phone' => '08111111111', 'name' => 'John PIC']);
        $crew = User::factory()->create(['phone' => '08222222222', 'name' => 'Jane Crew']);

        $response = $this->actingAs($gm)->post('/events', [
            'name' => 'Grand Concert',
            'description' => 'Annual big show',
            'location' => 'Main Hall',
            'event_dates' => '2026-07-10, 2026-07-11',
            'start_time' => '17:00',
            'end_time' => '22:00',
            'pic_id' => $pic->id,
            'positions' => [
                [
                    'name' => 'Sound Engineer',
                    'members' => [$crew->id],
                    'member_dates' => [
                        $crew->id => [
                            'work_dates' => '2026-07-10'
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertRedirect('/events');

        // Should send 2 WhatsApp messages (one to PIC, one to crew member)
        Http::assertSentCount(2);

        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628111111111') &&
                str_contains($request['message'], 'John PIC') &&
                str_contains($request['message'], 'Grand Concert') &&
                str_contains($request['message'], 'PIC Event');
        });

        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628222222222') &&
                str_contains($request['message'], 'Jane Crew') &&
                str_contains($request['message'], 'Grand Concert') &&
                str_contains($request['message'], 'Sound Engineer');
        });
    }

    public function test_event_update_triggers_notifications_only_to_newly_added_crew()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $gm = User::factory()->create();
        $gm->assignRole('GM');
        $gm->givePermissionTo('crud_events');

        $pic = User::factory()->create(['phone' => '08111111111', 'name' => 'John PIC']);
        $oldCrew = User::factory()->create(['phone' => '08222222222', 'name' => 'Jane Old Crew']);
        $newCrew = User::factory()->create(['phone' => '08333333333', 'name' => 'Bob New Crew']);

        // 1. Create event with oldCrew
        $event = Event::create([
            'name' => 'Exhibition',
            'event_dates' => ['2026-07-20'],
        ]);
        $event->participants()->sync([$pic->id => ['is_pic' => true]]);
        $pos = EventPosition::create(['event_id' => $event->id, 'name' => 'Stage Hand']);
        $pos->members()->sync([$oldCrew->id => ['work_dates' => json_encode(['2026-07-20'])]]);

        // Reset Http fakes count before update
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        // 2. Update event adding newCrew and keeping oldCrew
        $response = $this->actingAs($gm)->put("/events/{$event->id}", [
            'name' => 'Exhibition Updated',
            'event_dates' => '2026-07-20',
            'pic_id' => $pic->id,
            'positions' => [
                [
                    'id' => $pos->id,
                    'name' => 'Stage Hand',
                    'members' => [$oldCrew->id, $newCrew->id],
                    'member_dates' => [
                        $oldCrew->id => [
                            'work_dates' => '2026-07-20'
                        ],
                        $newCrew->id => [
                            'work_dates' => '2026-07-20'
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertRedirect('/events');

        // Should ONLY send message to newCrew (Bob New Crew)
        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628333333333') &&
                str_contains($request['message'], 'Bob New Crew') &&
                str_contains($request['message'], 'Exhibition Updated') &&
                str_contains($request['message'], 'Stage Hand');
        });
    }

    public function test_task_creation_triggers_notification_to_assignee()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $pic = User::factory()->create(['phone' => '08111111111']);
        $pic->assignRole('Employee');

        $event = Event::create(['name' => 'Festival', 'event_dates' => ['2026-07-20']]);
        $event->participants()->sync([$pic->id => ['is_pic' => true]]);

        $response = $this->actingAs($pic)->postJson("/events/{$event->id}/tasks", [
            'task_name' => 'Install sound monitors',
            'category' => 'pre',
            'assigned_to' => $pic->id
        ]);

        $response->assertStatus(200);

        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628111111111') &&
                str_contains($request['message'], 'INFO PENUGASAN EVENT') &&
                str_contains($request['message'], 'Festival');
        });
    }

    public function test_leave_request_submission_approvals_triggers_notifications()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $employee = User::factory()->create(['phone' => '08222222222', 'name' => 'Sherlock']);
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $direktur = User::factory()->create(['phone' => '08111111111', 'name' => 'Direktur Bobby']);
        $direktur->assignRole('Direktur');
        $direktur->givePermissionTo('leave_approvals');

        // 1. Submit leave request (type 'izin' for single approval flow testing)
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'izin',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'reason' => 'Sakit gigi',
            'proof' => \Illuminate\Http\UploadedFile::fake()->create('proof.jpg', 100)
        ]);

        $response->assertRedirect('/leave-requests');

        // Director/GM notified
        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628111111111') &&
                str_contains($request['message'], 'PENGAJUAN IZIN/CUTI BARU') &&
                str_contains($request['message'], 'Sherlock') &&
                str_contains($request['message'], 'Sakit gigi');
        });

        // Clear Http calls count
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $leave = LeaveRequest::where('user_id', $employee->id)->firstOrFail();

        // 2. Approve request by Direktur (single approval immediately approves 'izin')
        $response = $this->actingAs($direktur)->post("/leave-approvals/{$leave->id}/approve");
        $response->assertRedirect();

        // Employee notified of approval
        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628222222222') &&
                str_contains($request['message'], 'STATUS IZIN: DISETUJUI') &&
                str_contains($request['message'], 'DISETUJUI');
        });

        // Reset and test reject
        $leave->update(['status' => 'pending']);
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        // 3. Reject request by Direktur
        $response = $this->actingAs($direktur)->post("/leave-approvals/{$leave->id}/reject");
        $response->assertRedirect();

        // Employee notified of rejection
        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628222222222') &&
                str_contains($request['message'], 'STATUS PENGAJUAN: DITOLAK') &&
                str_contains($request['message'], 'DITOLAK');
        });
    }

    public function test_cuti_leave_request_dual_approval_triggers_notifications()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $employee = User::factory()->create(['phone' => '08222222222', 'name' => 'Sherlock']);
        $employee->assignRole('Employee');
        $employee->givePermissionTo('leave_request');

        $gm = User::factory()->create(['phone' => '08333333333', 'name' => 'GM Gary']);
        $gm->assignRole('GM');
        $gm->givePermissionTo('leave_approvals');

        $direktur = User::factory()->create(['phone' => '08111111111', 'name' => 'Direktur Bobby']);
        $direktur->assignRole('Direktur');
        $direktur->givePermissionTo('leave_approvals');

        // 1. Submit cuti request
        $response = $this->actingAs($employee)->post('/leave-requests', [
            'type' => 'cuti',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-03',
            'reason' => 'Family vacation'
        ]);

        $response->assertRedirect('/leave-requests');

        $leave = LeaveRequest::where('user_id', $employee->id)->firstOrFail();

        // 2. Approve by GM (still pending, no notification of final approval to employee yet)
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);
        $response = $this->actingAs($gm)->post("/leave-approvals/{$leave->id}/approve");
        $response->assertRedirect();

        // Confirm employee has not been notified of final approval yet
        Http::assertNotSent(function ($request) {
            return str_contains($request['target'], '628222222222') &&
                str_contains($request['message'], 'STATUS CUTI: DISETUJUI');
        });

        // 3. Approve by Direktur (now fully approved, sends notification to employee)
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);
        $response = $this->actingAs($direktur)->post("/leave-approvals/{$leave->id}/approve");
        $response->assertRedirect();

        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628222222222') &&
                str_contains($request['message'], 'STATUS CUTI: DISETUJUI') &&
                str_contains($request['message'], 'disetujui sepenuhnya oleh GM & Direktur');
        });
    }

    public function test_calendar_working_day_override_triggers_notifications()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $superadmin = User::factory()->create();
        $superadmin->assignRole('Superadmin');

        $employee = User::factory()->create(['phone' => '08222222222']);
        $employee->assignRole('Employee');

        // Change Saturday (weekend) 2026-06-13 to a working day
        $response = $this->actingAs($superadmin)->post('/settings/calendar', [
            'dates' => [
                '2026-06-13' => [
                    'is_working_day' => '1',
                    'description' => 'Emergency Operations'
                ]
            ]
        ]);

        $response->assertRedirect();

        // Employee notified
        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), 'api.fonnte.com/send')) {
                return false;
            }
            return str_contains($request['target'], '628222222222') &&
                str_contains($request['message'], 'pemberitahuan penting') &&
                str_contains($request['message'], 'Sabtu') &&
                str_contains($request['message'], '13 Juni 2026') &&
                str_contains($request['message'], 'tetap masuk kerja');
        });
    }

    public function test_time_driven_weekly_plan_reminders()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $employee = User::factory()->create(['phone' => '08222222222', 'name' => 'David']);
        $employee->assignRole('Employee');
        $employee->givePermissionTo('weekly_report');

        // Test Sunday reminder (target week starts next Monday)
        Artisan::call('weekly:sunday-plan-reminder');

        Http::assertSent(function ($request) {
            return str_contains($request['target'], '628222222222') &&
                str_contains($request['message'], 'belum membuat Weekly Plan untuk minggu ini');
        });
    }
}
