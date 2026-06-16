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

    public function test_Direktur_with_rekap_event_permission_can_access_event_recaps()
    {
        Role::firstOrCreate(['name' => 'Direktur']);
        
        $Direktur = User::factory()->create();
        $Direktur->assignRole('Direktur');
        $Direktur->givePermissionTo('rekap_event');

        $response = $this->actingAs($Direktur)->get('/event-recaps');
        $response->assertStatus(200);
    }

    public function test_Direktur_without_rekap_event_permission_cannot_access_event_recaps()
    {
        Role::firstOrCreate(['name' => 'Direktur']);
        
        $Direktur = User::factory()->create();
        $Direktur->assignRole('Direktur');

        $response = $this->actingAs($Direktur)->get('/event-recaps');
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

    public function test_pic_cannot_manually_add_forbidden_categories_or_automatic_name()
    {
        $pic = User::factory()->create();
        $event = Event::create(['name' => 'Event PIC Test']);
        $event->participants()->attach($pic->id, ['is_pic' => true]);
        
        EventRecap::create([
            'event_id' => $event->id,
            'initial_nominal' => 1000000,
            'status' => 'dalam_rekap',
        ]);

        // 1. Try to add Pemasukan category
        $response1 = $this->actingAs($pic)->post("/event-recaps/{$event->id}/items", [
            'date' => '2026-06-05',
            'category' => 'Pemasukan',
            'vendor' => 'Test Vendor',
            'item_name' => 'Manual Topup',
            'quantity' => 1,
            'unit_price' => 100000,
            'receipt' => \Illuminate\Http\UploadedFile::fake()->image('receipt.jpg'),
        ]);
        $response1->assertRedirect();
        $response1->assertSessionHas('error', 'Kategori ini hanya dapat ditambahkan secara otomatis oleh sistem/Finance.');

        // 2. Try to add Pengurangan Anggaran category
        $response2 = $this->actingAs($pic)->post("/event-recaps/{$event->id}/items", [
            'date' => '2026-06-05',
            'category' => 'Pengurangan Anggaran',
            'vendor' => 'Test Vendor',
            'item_name' => 'Manual Deduction',
            'quantity' => 1,
            'unit_price' => 100000,
            'receipt' => \Illuminate\Http\UploadedFile::fake()->image('receipt.jpg'),
        ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('error', 'Kategori ini hanya dapat ditambahkan secara otomatis oleh sistem/Finance.');

        // 3. Try to add Penyesuaian Anggaran (Otomatis) item name
        $response3 = $this->actingAs($pic)->post("/event-recaps/{$event->id}/items", [
            'date' => '2026-06-05',
            'category' => 'Konsumsi',
            'vendor' => 'Test Vendor',
            'item_name' => 'Penyesuaian Anggaran (Otomatis)',
            'quantity' => 1,
            'unit_price' => 100000,
            'receipt' => \Illuminate\Http\UploadedFile::fake()->image('receipt.jpg'),
        ]);
        $response3->assertRedirect();
        $response3->assertSessionHas('error', 'Nama item ini dicadangkan untuk penyesuaian anggaran otomatis.');
    }

    public function test_pic_cannot_delete_automatic_adjustments()
    {
        $pic = User::factory()->create();
        $event = Event::create(['name' => 'Event PIC Delete Test']);
        $event->participants()->attach($pic->id, ['is_pic' => true]);

        EventRecap::create([
            'event_id' => $event->id,
            'initial_nominal' => 1000000,
            'status' => 'dalam_rekap',
        ]);

        $item = EventRecapItem::create([
            'event_id' => $event->id,
            'date' => '2026-06-05',
            'category' => 'Pemasukan',
            'item_name' => 'Penyesuaian Anggaran (Otomatis)',
            'vendor' => 'Finance',
            'quantity' => 1,
            'unit_price' => 200000,
            'nominal' => 200000,
            'receipt_path' => '',
            'uploader_id' => $pic->id,
        ]);

        $response = $this->actingAs($pic)->delete("/event-recaps/{$event->id}/items/{$item->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tidak dapat menghapus item penyesuaian anggaran otomatis.');
        $this->assertDatabaseHas('event_recap_items', ['id' => $item->id]);
    }

    public function test_xls_export_calculates_correct_saldo_awal_with_adjustments()
    {
        $division = Division::create(['name' => 'Finance']);
        $finance = User::factory()->create([
            'division_id' => $division->id,
        ]);
        $finance->givePermissionTo('rekap_event');

        $event = Event::create([
            'name' => 'XLS Export Test Event',
        ]);

        $recap = EventRecap::create([
            'event_id' => $event->id,
            'initial_nominal' => 1000000, // Saldo awal
            'status' => 'dalam_rekap',
        ]);

        // Finance increases the budget by 200000 (recap initial_nominal becomes 1200000)
        $this->actingAs($finance)->post("/event-recaps/{$event->id}/budget", [
            'initial_nominal' => 1200000,
        ]);

        // Eager load items and assert database changes
        $recap->refresh();
        $this->assertEquals(1200000, (float)$recap->initial_nominal);

        // Fetch the export route
        $response = $this->actingAs($finance)->get("/event-recaps/{$event->id}/export");
        $response->assertStatus(200);

        // Assert view data contains the correct variables
        $viewData = $response->original->getData();
        $this->assertArrayHasKey('recap', $viewData);
        $this->assertArrayHasKey('items', $viewData);

        // Render view HTML and check if "Saldo Awal" remains 1000000
        $html = $response->getContent();
        $this->assertStringContainsString('Rp 1.000.000', $html);
        $this->assertStringContainsString('Rp 1.200.000', $html); // Grand total debet
    }
}

