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
        Schema::create('event_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('task_name');
            $table->enum('category', ['pre', 'dday', 'post']); // Fase Event
            $table->enum('type', ['official', 'personal'])->default('official'); // Jenis Tugas
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('cascade'); // Penerima Tugas
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Pembuat Tugas
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tasks');
    }
};
