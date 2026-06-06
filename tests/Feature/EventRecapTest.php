<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRecap;
use App\Models\EventRecapItem;
use App\Models\Division;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRecapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Roles and Permissions
        Role::firstOrCreate(['name' => 'Employee']);
        Permission::firstOrCreate(['name' => 'rekap_event']);
    }

    public function test_finance_can_update_budget_without_expected_receipts_count()
    {
        $division = Division::create(['name' => 'Finance']);
        $finance = User::factory()->create([
            'division_id' => $division->id,
        ]);
        $finance->givePermissionTo('rekap_event');

        $event = Event::create([
            'name' => 'Test Event',
            'description' => 'A test event description',
        ]);

        $response = $this->actingAs($finance)->post("/event-recaps/{$event->id}/budget", [
            'initial_nominal' => 5000000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Anggaran berhasil diperbarui.');

        $this->assertDatabaseHas('event_recaps', [
            'event_id' => $event->id,
            'initial_nominal' => 5000000,
        ]);
    }

    public function test_completion_score_calculation_without_receipt_target()
    {
        $event = Event::create([
            'name' => 'Score Test Event',
        ]);

        $recap = EventRecap::create([
            'event_id' => $event->id,
            'initial_nominal' => 1000000,
            'status' => 'dalam_rekap',
        ]);

        // Score with 0 receipts uploaded should have completenessScore = 0
        // Total score = completenessScore (0) + speedScore (5 default min) + statusScore (10 draft/dalam_rekap) = 15
        $this->assertEquals(15, $recap->completion_score);

        // Upload a receipt
        $uploader = User::factory()->create();
        EventRecapItem::create([
            'event_id' => $event->id,
            'date' => '2026-06-05',
            'category' => 'Konsumsi',
            'vendor' => 'Test Vendor',
            'nominal' => 100000,
            'receipt_path' => 'receipts/test.jpg',
            'uploader_id' => $uploader->id,
        ]);

        // Refresh recap relation/data
        $recap->load('event.recapItems');

        // Score with 1 receipt uploaded should have completenessScore = 40
        // Total score = completenessScore (40) + speedScore (5 default min) + statusScore (10 draft/dalam_rekap) = 55
        $this->assertEquals(55, $recap->completion_score);
    }

    public function test_ceo_with_rekap_event_permission_can_access_event_recaps()
    {
        Role::firstOrCreate(['name' => 'CEO']);
        
        $ceo = User::factory()->create();
        $ceo->assignRole('CEO');
        $ceo->givePermissionTo('rekap_event');

        $response = $this->actingAs($ceo)->get('/event-recaps');
        $response->assertStatus(200);
    }

    public function test_ceo_without_rekap_event_permission_cannot_access_event_recaps()
    {
        Role::firstOrCreate(['name' => 'CEO']);
        
        $ceo = User::factory()->create();
        $ceo->assignRole('CEO');

        $response = $this->actingAs($ceo)->get('/event-recaps');
        $response->assertStatus(403);
    }

    public function test_finance_without_rekap_event_permission_can_access_event_recaps()
    {
        $division = Division::create(['name' => 'Finance']);
        $finance = User::factory()->create([
            'division_id' => $division->id,
        ]);

        $response = $this->actingAs($finance)->get('/event-recaps');
        $response->assertStatus(200);
    }
}

