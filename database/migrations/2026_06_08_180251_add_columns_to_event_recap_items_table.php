<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_recap_items', function (Blueprint $table) {
            // Menambah kolom NAMA ITEM, QTY, HARGA SATUAN, dan KETERANGAN
            $table->string('item_name')->nullable()->after('category');
            $table->integer('quantity')->default(1)->after('description');
            $table->decimal('unit_price', 15, 2)->default(0)->after('quantity');
            $table->text('notes')->nullable()->after('nominal');
        });
    }

    public function down(): void
    {
        Schema::table('event_recap_items', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'quantity', 'unit_price', 'notes']);
        });
    }
};