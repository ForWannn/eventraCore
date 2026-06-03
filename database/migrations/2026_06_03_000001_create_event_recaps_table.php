<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_recaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained('events')->onDelete('cascade');
            $table->decimal('initial_nominal', 15, 2)->default(0);
            $table->integer('expected_receipts_count')->default(10);
            $table->string('status')->default('draft'); // draft, dalam_rekap, menunggu_finance, direvisi, selesai
            $table->integer('speed_percentage')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_recaps');
    }
};
