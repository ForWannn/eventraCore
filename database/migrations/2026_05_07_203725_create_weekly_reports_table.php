<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('weekly_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->date('week_start_date'); // Tanggal hari Senin
        $table->text('notes')->nullable();
        $table->enum('status', ['draft', 'submitted'])->default('draft');
        $table->timestamp('plan_submitted_at')->nullable();
        $table->timestamp('final_submitted_at')->nullable();
        $table->timestamps();
    });

    Schema::create('weekly_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('weekly_report_id')->constrained()->onDelete('cascade');
        $table->enum('type', ['objective', 'deadline']);
        $table->string('content');
        $table->boolean('is_completed')->default(false);
        $table->timestamps();
    });

    Schema::create('daily_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('weekly_report_id')->constrained()->onDelete('cascade');
    $table->date('log_date'); // Gunakan nama log_date
    $table->text('description')->nullable();
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
