<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class WorkCalendarHolidayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Employee']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage_calendar']);
        WorkCalendar::clearRuntimeCache();
        Cache::clear();
    }

    public function test_is_working_day_with_national_holiday_auto_detection()
    {
        // Mock holiday API response for year 2026
        Http::fake([
            'https://libur.deno.dev/api?year=2026' => Http::response([
                [
                    'date' => '2026-08-17',
                    'name' => 'Hari Kemerdekaan RI',
                    'is_national_holiday' => true,
                ],
                [
                    'date' => '2026-12-25',
                    'name' => 'Hari Natal',
                    'is_national_holiday' => true,
                ]
            ], 200)
        ]);

        // 2026-08-17 is a Monday. Since it is a national holiday, it should default to false (not a working day)
        $this->assertFalse(WorkCalendar::isWorkingDay('2026-08-17'));
        $this->assertEquals('Hari Kemerdekaan RI', WorkCalendar::getHolidayDescription('2026-08-17'));

        // A regular weekday (e.g. 2026-08-18, Tuesday) should be a working day
        $this->assertTrue(WorkCalendar::isWorkingDay('2026-08-18'));
        $this->assertNull(WorkCalendar::getHolidayDescription('2026-08-18'));

        // Weekend (e.g. 2026-08-16, Sunday) should not be a working day
        $this->assertFalse(WorkCalendar::isWorkingDay('2026-08-16'));

        // Create database override for 2026-08-17 to make it a working day
        WorkCalendar::create([
            'date' => '2026-08-17',
            'is_working_day' => true,
            'description' => 'Kerja Bhakti Kemerdekaan',
        ]);

        // Now it should be a working day due to override
        $this->assertTrue(WorkCalendar::isWorkingDay('2026-08-17'));
        $this->assertEquals('Kerja Bhakti Kemerdekaan', WorkCalendar::getHolidayDescription('2026-08-17'));
    }

    public function test_settings_calendar_view_displays_auto_detected_holidays()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $admin->givePermissionTo('manage_calendar');

        // Mock holiday API response
        Http::fake([
            'https://libur.deno.dev/api?year=2026' => Http::response([
                [
                    'date' => '2026-08-17',
                    'name' => 'Hari Kemerdekaan RI',
                    'is_national_holiday' => true,
                ]
            ], 200)
        ]);

        $response = $this->actingAs($admin)->get('/settings/calendar?year=2026&month=08');
        
        $response->assertStatus(200);
        $response->assertSee('Hari Kemerdekaan RI');
    }
}
