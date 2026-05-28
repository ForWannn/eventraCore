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
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['pic_fee', 'loading_fee', 'unloading_fee']);
        });

        Schema::table('event_positions', function (Blueprint $table) {
            $table->dropColumn('fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('pic_fee', 15, 2)->default(0);
            $table->decimal('loading_fee', 15, 2)->default(0);
            $table->decimal('unloading_fee', 15, 2)->default(0);
        });

        Schema::table('event_positions', function (Blueprint $table) {
            $table->decimal('fee', 15, 2)->default(0);
        });
    }
};
